<?php
class Drupal_ApiExtend_Model_Quote_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Create new quote
	 *
	 * @return array
	 */
	public function create($orderData = null)
	{
		$checkout = Mage::getSingleton('checkout/session');
		$quote    = $checkout->getQuote();

		// look up customer
		$customer = Mage::getModel('customer/customer')
		->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
		->load($orderData['magento_user']);

		$quote->assignCustomer($customer);
		$quote->setIsMultiShipping(false);
		$quote->save();

		$address     = $quote->getShippingAddress();
		$address->setCollectShippingRates(true);
        
        $quote->setShippingAddress($address);
		$quote->collectTotals()->save();

		// set shipping method
		$quote->getShippingAddress()->setShippingMethod(null);
		$quote->collectTotals()->save();

		// set payment
		$payment = $quote->getPayment();
		$payment->importData(array(
		    		'method' => 'free',
		//		    		'po_number' => $xml->getPoNumber()
		));
		$quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
		$quote->collectTotals()->save();
		
		$result = array();
		
		// add products
		foreach ($orderData['products'] as $orderProduct) {
			$product = Mage::getModel('catalog/product');
			/* @var $product Mage_Catalog_Model_Product */

			$store = Mage::app()->getStore()->getStoreId();
			$product->setStoreId($store);
			$product->load($orderProduct['id']);
			$quote->addProduct($product, intval($orderProduct['qty']));
			$result['products'][$product->getId()] = array(
				'price' => $product->getFinalPrice($orderProduct['qty']),
				'qty' => $orderProduct['qty'],
			); 
		}

		// save quote
		$quote->collectTotals()->save();

		$result['subtotal'] = $quote->getSubtotal();
		$result['discount_amount'] = $quote->getShippingAddress()->getDiscountAmount();
		$result['grand_total'] = $quote->getBaseGrandTotal();
		$result['taxes'] = $quote->getShippingAddress()->getAppliedTaxes();
		
		return $result;
	}
} // Class Drupal_ApiExtend_Model_Quote_Api End

