<?php
class Drupal_ApiExtend_Model_Config_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Retrieve Magento config value
	 *
	 * @return array
	 */
	public function info($path, $storeId) {
		try {
			$result = Mage::getStoreConfig($path, $storeId);
			return $result;
		} catch (Mage_Core_Exception $e) {
			$this->_fault("can't get config info", $e->getMessage());
		}
	}

	/**
	 * Retrieve Magento Websites list
	 *
	 * @return array
	 */
	public function websites() {
		$result = array();
		foreach (Mage::getModel('core/website')->getCollection() as $website) {
			$result[] = $website->toArray();
		}
		return $result;
	}
	
	/**
	 * Retrieve Magento Stores list
	 *
	 * @return array
	 */
	public function stores() {
		$result = array();
		foreach (Mage::getModel('core/store_group')->getCollection() as $store) {
			$result[] = $store->toArray();
		}
		return $result;
	}
	
	/**
	 * Retrieve Magento Store Views list
	 *
	 * @return array
	 */
	public function storeViews() {
		$result = array();
		foreach (Mage::getModel('core/store')->getCollection() as $storeView) {
			$result[] = $storeView->toArray();
		}
		return $result;
	}
	
} // Class Drupal_ApiExtend_Model_Config_Api End

