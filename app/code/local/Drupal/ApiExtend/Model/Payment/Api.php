<?php
class Drupal_ApiExtend_Model_Payment_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Retrieve active payment methods
	 *
	 * @return array
	 */
	public function items()
	{
		$payments = Mage::getSingleton('payment/config')->getActiveMethods();
		$methods = array();
		foreach ($payments as $paymentCode=>$paymentModel) {
			if (strtoupper($paymentCode) != 'GOOGLECHECKOUT') {
				$paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
				$methods[$paymentCode] = $paymentTitle;
			}
		}
		return $methods;
	}

	public function PayPalWPSAccount() {
		$result = array();
		$result['account'] = Mage::getStoreConfig('paypal/wps/business_account');
		$result['name'] = Mage::getStoreConfig('paypal/wps/business_name');
		return $result;
	}
} // Class Drupal_ApiExtend_Model_Payment_Api End

