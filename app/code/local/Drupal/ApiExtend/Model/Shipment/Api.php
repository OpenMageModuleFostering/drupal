<?php
class Drupal_ApiExtend_Model_Shipment_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Retrieve shipment methods
	 *
	 * @return array
	 */
	public function items()
	{
		$methods = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')
		->toOptionArray(true);
		return $methods;
	}
} // Class Drupal_ApiExtend_Model_Shipment_Api End

