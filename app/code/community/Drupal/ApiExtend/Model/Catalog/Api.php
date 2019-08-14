<?php
class Drupal_ApiExtend_Model_Catalog_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Retrieve products list
	 *
	 * @param array $filters
	 * @param int $storeId
	 * @return array
	 */
	public function productsList($filters, $storeId)
	{
		try {
			$store = Mage::app()->getStore($storeId)->getId();
		} catch (Mage_Core_Model_Store_Exception $e) {
			$this->_fault('store_not_exists');
		}

		$collection = Mage::getModel('catalog/product')->getCollection()
		->setStoreId($store)
		// getting most common attributes for saving on Drupal side
		->addAttributeToSelect('name')
		->addAttributeToSelect('price')
		->addAttributeToSelect('status')
		->addAttributeToSelect('special_price')
		->addAttributeToSelect('special_from_date')
		->addAttributeToSelect('special_to_date')
		->addAttributeToSelect('weight')
		->addAttributeToSelect('visibility')
		->addAttributeToSelect('news_from_date')
		->addAttributeToSelect('news_to_date')
		->addAttributeToSelect('description')
		->addAttributeToSelect('short_description');

		if (is_array($filters)) {
			try {
				foreach ($filters as $field => $value) {
					if (isset($this->_filtersMap[$field])) {
						$field = $this->_filtersMap[$field];
					}

					$collection->addFieldToFilter($field, $value);
				}
			} catch (Mage_Core_Exception $e) {
				$this->_fault('filters_invalid', $e->getMessage());
			}
		}

		$result = array();

		foreach ($collection as $product) {

			$all_images = Mage::getModel('catalog/product_attribute_media_api')->items($product->getId(), $store);

			$true_images = array();

			foreach ($all_images as $image) {
				if (in_array('image', $image['types'])) {
					$true_images[] = $image;
				}
			}
			$wId = Mage::app()->getStore($store)->getWebsiteId();

			$rules = Mage::getResourceModel('ApiExtend/rule')->getAllRulesForProduct(now(), $wId, $product->getId());

			
			// after call this method tier price appear in toArray() result.  
			$product->getTierPrice();
			// if call like this, category ids returns in array, not as string if keep toArray() style.
			$product->getCategoryIds();

			$product_array = $product->toArray();

			$product_array['images'] = $true_images;
			$product_array['rules'] = $rules;

			$result[] = $product_array;
		}

		return $result;
	}
	/**
	 * Retrieve actual product price
	 *
	 * @param int $productId
	 * @param int $qty
	 * @param int $groupId
	 * @param int $store
	 * @return array
	 */
	public function productActualPrice($productId, $qty, $gId, $store = null)
	{
		$product = Mage::getModel('catalog/product');
		/* @var $product Mage_Catalog_Model_Product */

		if (is_string($productId)) {
			$idBySku = $product->getIdBySku($productId);
			if ($idBySku) {
				$productId = $idBySku;
			}
		}

		$store = is_null($store) ? 0 : $store;
		try {
			$product->setStoreId($store);
		} catch (Mage_Core_Model_Store_Exception $e) {
			$this->_fault('store_not_exists');
		}

		$product->load($productId);

		if (!$product->getId()) {
			$this->_fault('not_exists');
		}
		$price = $product->getPrice();
		$tier_price = $product->getTierPrice($qty);
		if ($tier_price) {
			if ($tier_price < $price) $price = $tier_price;
		}

		$time = time();
		$special_price = $product = Mage::getModel('catalog/product_api')->getSpecialPrice($productId, $store = null);
		if ($special_price) {
			if ($special_price['special_price'] < $price && $time >= strtotime($special_price['special_from_date']) && $time <= 	strtotime($special_price['special_to_date'])) $price = $special_price['special_price'];
		}
		$wId = Mage::app()->getStore($store)->getWebsiteId();
		$rule_price = Mage::getResourceModel('catalogrule/rule')->getRulePrice($time, $wId, $gId, $productId);
		if ($rule_price) {
			if ($rule_price < $price) $price = $rule_price;
		}
		return $price;
	}

	/**
	 * Retrieve product full info (full attribute list with values)
	 *
	 * @param int $productId
	 * @param int $store
	 * @return array
	 */
	public function productFullInfo($productId, $store = null)
	{
		$product = Mage::getModel('catalog/product');

		/* @var $product Mage_Catalog_Model_Product */

		if (is_string($productId)) {
			$idBySku = $product->getIdBySku($productId);
			if ($idBySku) {
				$productId = $idBySku;
			}
		}

		$store = is_null($store) ? 0 : $store;
		try {
			$product->setStoreId($store);
		} catch (Mage_Core_Model_Store_Exception $e) {
			$this->_fault('store_not_exists');
		}

		$product->load($productId);

		//		error_log("sdsd", 3, "err.log");

		if (!$product->getId()) {
			$this->_fault('not_exists');
		}

		$setId = $product->getAttributeSetId();

		$result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
		);

		$attributes = Mage::getModel('catalog/product')->getResource()
		->loadAllAttributes()
		->getSortedAttributes($setId);

		foreach ($attributes as $attribute) {
			$result[$attribute->getAttributeCode()] = $product->getData(
			$attribute->getAttributeCode());
		}

		return $result;
	}


} // Class Drupal_ApiExtend_Model_Catalog_Api End

