<?php
class Drupal_ApiExtend_Model_Order_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Retrieve order create model
	 *
	 * @return  Mage_Adminhtml_Model_Sales_Order_Create
	 */
	protected function _getOrderCreateModel()
	{
		return Mage::getSingleton('adminhtml/sales_order_create');
	}

	/**
	 * Retrieve session object
	 *
	 * @return  Mage_Adminhtml_Model_Session_Quote
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session_quote');
	}

	/**
	 * Initialize order creation session data
	 *
	 * @param   array $data
	 * @return  Mage_Adminhtml_Sales_Order_CreateController
	 */
	protected function _initSession($data)
	{
		/**
		 * Identify customer
		 */
		if (!empty($data['customer_id'])) {
			$this->_getSession()->setCustomerId((int) $data['customer_id']);
		}

		/**
		 * Identify store
		 */
		if (!empty($data['store_id'])) {
			$this->_getSession()->setStoreId((int) $data['store_id']);
		}

		return $this;
	}

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _initCheckout($data)
    {
        $session =  Mage::getSingleton('checkout/session');
        $session->setCustomerId((int) $data['customer_id']);
        $session->setStoreId((int) $data['store_id']);
        $session->getQuote()->save();
        return $session;
    }
	
	/**
	 * Processing quote data
	 *
	 * @param   array $data
	 * @return  Yournamespace_Yourmodule_IndexController
	 */
	protected function _processQuote($data = array())
	{
		/**
		 * Saving order data
		 */
		if (!empty($data['order'])) {
			$this->_getOrderCreateModel()->importPostData($data['order']);
		}

		/**
		 * init first billing address, need for virtual products
		 */
		$this->_getOrderCreateModel()->getBillingAddress();
		$this->_getOrderCreateModel()->setShippingAsBilling(true);

		/**
		 * Adding products to quote from special grid and
		 */
		if (!empty($data['add_products'])) {
			$this->_getOrderCreateModel()->addProducts($data['add_products']);
		}

		/**
		 * Collecting shipping rates
		 */
		$this->_getOrderCreateModel()->collectShippingRates();

		/**
		 * Adding payment data
		 */
		if (!empty($data['payment'])) {
			$this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
		}

		$this->_getOrderCreateModel()
		->initRuleData()
		->saveQuote();

		if (!empty($data['payment'])) {
			$this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
		}

		return $this;
	}

	/**
	 * Prepare order data
	 *
	 * @param array $orderData
	 * @return array
	 */
	protected function _prepareOrderData($orderData) {
		$customer = Mage::getModel('customer/customer')->load($orderData['magento_user']);
		//        error_log("_prepareOrderData: \n", 3, "errr.log");
		//		$customer = Mage::getModel('customer/customer')->load(2);

		$result = array();
		$result['session']       = array(
        	'customer_id'   => $orderData['magento_user'],
        	'store_id'      => Mage::app()->getStore()->getStoreId(),
		);
		$result['payment']       = array(
        	'method'    => $orderData['payment_method'],
		    'po_number' => '',
			'cc_type'=> '',
			'cc_number'=> '',
			'cc_last4' => '',
			'cc_owner' => '',
			'cc_exp_month' => '',
			'cc_exp_year' => '',
			'cc_ss_start_month' => '',
			'cc_ss_start_year' => '',
		);
		$result['order']['currency']  = Mage::app()->getStore()->getBaseCurrencyCode();
		$result['order']['account']   = array(
            	'group_id'  => $customer->getGroupId(),
            	'email'     => (string) $customer->getEmail(),
		);
		$result['order']['comment'] = array('customer_note' => 'API ORDER');
		$result['order']['send_confirmation'] = 1;
		$result['order']['shipping_method']   = $orderData['shipment_method'];
		$result['order']['billing_address']   = $orderData['billing_information'];
		$result['order']['shipping_address']  = $orderData['shipping_information'];

		foreach ($orderData['products'] as $product) {
			$result['add_products'][$product['id']] = array('qty' => $product['qty']);
		}
		return $result;
	}

	/**
	 * Create new order
	 *
	 * @return array
	 */
	public function create($orderData = null)
	{
		$orderData = $this->_prepareOrderData($orderData);
		if (!empty($orderData)) {
			// we have valid order data
			$this->_initSession($orderData['session']);
			try {
				$this->_processQuote($orderData);
				
				$this->_initCheckout($orderData['session']);
				
				if (!empty($orderData['payment'])) {
					$this->_getOrderCreateModel()->setPaymentData($orderData['payment']);
					$this->_getOrderCreateModel()->getQuote()->getPayment()->addData($orderData['payment']);
				}
				
				$order = $this->_getOrderCreateModel()
				->importPostData($orderData['order'])
				->createOrder();

				$order_id = $order->getIncrementId();

				$result = array();
				$result['order_increment_id'] = $order_id;
				$result['shipping_amount'] = $order->getData('shipping_amount');
				$result['subtotal'] = $order->getData('subtotal');
				$result['grand_total'] = $order->getData('total_due');
				$result['tax_amount'] = $order->getData('tax_amount');
				$result['discount_amount'] = $order->getData('discount_amount');
				
				$this->_getSession()->clear();
				Mage::unregister('rule_data');
				Mage::log('Order Successfull', Zend_Log::INFO);
				
//				error_log("return: \n".var_export($result, true), 3, "errr.log");
				return $result;
			}
			catch (Exception $e){
//				error_log(sprintf('Order saving error: %s', $e->getMessage()), 3, "errr.log");
				Mage::log(sprintf('Order saving error: %s', $e->getMessage()), Zend_Log::ERR);
			}
		}
	}
} // Class Drupal_ApiExtend_Model_Order_Api End

