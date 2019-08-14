<?php
/**
 * Extended Catalog rules resource model
 */
class Drupal_ApiExtend_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{	  
	/**
	 * Initialize main table and table id field
	 */
	protected function _construct()
	{
		$this->_init('catalogrule/rule', 'rule_id');
	}

	/**
	 * Get data about rules for all customer groups
	 *
	 * @param   int|string $date
	 * @param   int $wId
	 * @param   int $pId
	 * @return  array
	 */
	public function getAllRulesForProduct($date, $wId, $pId)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
		->from(
			array('tbl_prices' => $this->getTable('catalogrule/rule_product_price')),
			array('rule_price', 'customer_group_id', 'website_id', 'product_id'))
		->join(
			array('tbl_rules' => $this->getTable('catalogrule/rule_product')),
            '`tbl_rules`.`product_id` = `tbl_prices`.`product_id` and'.
            '`tbl_rules`.`website_id` = `tbl_prices`.`website_id` and'.
			'`tbl_rules`.`customer_group_id` = `tbl_prices`.`customer_group_id`',
			array('rule_id')
		)
		->where('`tbl_prices`.`rule_date`=?', $this->formatDate($date, false))
		->where('`tbl_prices`.`website_id`=?', $wId)
		->where('`tbl_prices`.`product_id`=?', $pId);
		return $read->fetchAll($select);
		
	}
}