<?php
//require_once('../app/Mage.php');
require_once 'Zend/XmlRpc/Client.php';

class Drupal_ApiExtend_Model_Observer
{

	const EVENT_NOTIFICATION_ENABLED = 'extended_api/config/enable_event_notification';
	const EVENT_NOTIFICATION_REMOTE_HOST = 'extended_api/config/remote_xmlrpc_host';
	const EVENT_NOTIFICATION_REMOTE_USERNAME = 'extended_api/config/remote_xmlrpc_username';
	const EVENT_NOTIFICATION_REMOTE_PASSWORD = 'extended_api/config/remote_xmlrpc_password';

	/**
	 * Process catalog category after delete
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Drupal_ApiExtend_Model_Observer
	 */
	public function catalogCategoryDeleteAfter(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig(self::EVENT_NOTIFICATION_ENABLED,000)) {
			//			$eventCategory = $observer->getEvent()->getCategory();
			//			$categoryId = $eventCategory->getId();

			$tree = Mage::getModel('catalog/category_api')->tree();
				
			$host = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_HOST,000);
			$username = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_USERNAME, 000);
			$password = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_PASSWORD, 000);
			if ($host) {
				$client = new Zend_XmlRpc_Client($host);
				$sessId = $client->call('system.connect', array());
				if ($sessId) {
					$sessId = $client->call('user.login', array($sessId['sessid'], $username, $password));
					if ($sessId) {
						$result = $client->call('magento_api.catalogCategoryDeleteEventHandler', array($sessId['sessid'], $tree));
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Process catalog category after delete
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Drupal_ApiExtend_Model_Observer
	 */
	public function catalogCategorySaveAfter(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig(self::EVENT_NOTIFICATION_ENABLED,000)) {
				
			//			$eventCategory = $observer->getEvent()->getCategory();
			//			$categoryId = $eventCategory->getId();
				
			$tree = Mage::getModel('catalog/category_api')->tree();

			$host = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_HOST,000);
			$username = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_USERNAME, 000);
			$password = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_PASSWORD, 000);
			if ($host) {
				$client = new Zend_XmlRpc_Client($host);
				$sessId = $client->call('system.connect', array());
				if ($sessId) {
					$sessId = $client->call('user.login', array($sessId['sessid'], $username, $password));
					if ($sessId) {
						$result = $client->call('magento_api.catalogCategorySaveEventHandler', array($sessId['sessid'], $tree));
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Process catalog product after save
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Drupal_ApiExtend_Model_Observer
	 */
	public function catalogProductSaveAfter(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig(self::EVENT_NOTIFICATION_ENABLED,000)) {
			$eventProduct = $observer->getEvent()->getProduct();
			$productId = $eventProduct->getId();

			$host = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_HOST,000);
			$username = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_USERNAME, 000);
			$password = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_PASSWORD, 000);
			if ($host) {
				$client = new Zend_XmlRpc_Client($host);
				$sessId = $client->call('system.connect', array());
				if ($sessId) {
					$sessId = $client->call('user.login', array($sessId['sessid'], $username, $password));
					if ($sessId) {
						$result = $client->call('magento_api.productSaveEventHandler', array($sessId['sessid'], $productId));
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Process catalog product after delete
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Drupal_ApiExtend_Model_Observer
	 */
	public function catalogProductDeleteAfter(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig(self::EVENT_NOTIFICATION_ENABLED,000)) {
			$eventProduct = $observer->getEvent()->getProduct();
			$productId = $eventProduct->getId();

			$host = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_HOST,000);
			$username = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_USERNAME, 000);
			$password = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_PASSWORD, 000);
			if ($host) {
				$client = new Zend_XmlRpc_Client($host);
				$sessId = $client->call('system.connect', array());
				if ($sessId) {
					$sessId = $client->call('user.login', array($sessId['sessid'], $username, $password));
					if ($sessId) {
						$result = $client->call('magento_api.productDeleteEventHandler', array($sessId['sessid'], $productId));
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Update Only product status observer
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Drupal_ApiExtend_Model_Observer
	 */
	public function productStatusUpdate(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig(self::EVENT_NOTIFICATION_ENABLED,000)) {
			$productId = $observer->getEvent()->getProductId();

			$host = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_HOST,000);
			$username = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_USERNAME, 000);
			$password = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_PASSWORD, 000);
			if ($host) {
				$client = new Zend_XmlRpc_Client($host);
				$sessId = $client->call('system.connect', array());
				if ($sessId) {
					$sessId = $client->call('user.login', array($sessId['sessid'], $username, $password));
					if ($sessId) {
						$result = $client->call('magento_api.productStatusUpdateEventHandler', array($sessId['sessid'], $productId));
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Process catalog rule after apply
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Drupal_ApiExtend_Model_Observer
	 */
	public function catalogRuleApplyAfter(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig(self::EVENT_NOTIFICATION_ENABLED,000)) {
			try {
				$host = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_HOST,000);
				$username = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_USERNAME, 000);
				$password = Mage::getStoreConfig(self::EVENT_NOTIFICATION_REMOTE_PASSWORD, 000);
				if ($host) {
					$client = new Zend_XmlRpc_Client($host);
					$sessId = $client->call('system.connect', array());
					if ($sessId) {
						$sessId = $client->call('user.login', array($sessId['sessid'], $username, $password));
						if ($sessId) {
							$result = $client->call('magento_api.catalogRuleApplyAfterEventHandler', array($sessId['sessid']));
						}
					}
				}
			}
			catch (Exception $e){
				Mage::log(sprintf('Catalog rule after apply event notification error: %s', $e->getMessage()), Zend_Log::ERR);
			}
		}
		return $this;
	}
}