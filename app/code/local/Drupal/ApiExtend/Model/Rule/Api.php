<?php
class Drupal_ApiExtend_Model_Rule_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Retrieve rules list
	 *
	 * @return array
	 */
	public function rulesList()
	{
		$rules = Mage::getModel('catalogrule/rule')->getCollection();
        $result = $rules->toArray();    

		return $result;
	}

} // Class Drupal_ApiExtend_Model_Rule_Api End

