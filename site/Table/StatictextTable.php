<?php
/**
* @version      5.1.2 24.12.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class StaticTextTable extends ShopbaseTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_config_statictext', 'id', $_db );
    }
    
    function loadData($alias){
        $lang = JSFactory::getLang();
        $db = Factory::getDBO();         
        $query = "SELECT id, alias, `".$lang->get('text')."` as text FROM `#__jshopping_config_statictext` where alias='".$db->escape($alias)."'";
		Factory::getApplication()->triggerEvent('onBeforeLoadDataStaticTextTable', array(&$query, &$alias));
        $db->setQuery($query);
        return $db->loadObJect();
    }
    
    function loadDataByIds($list){
        if (!count($list)){
            return array();
        }
        $lang = JSFactory::getLang();
        $db = Factory::getDBO();  
        $ids = implode(',', $list);
        $query = "SELECT id, alias, `".$lang->get('text')."` as text FROM `#__jshopping_config_statictext` where id IN (".$db->escape($ids).")";
        $db->setQuery($query);
        return $db->loadObJectList();
    }
    
    function getReturnPolicyForProducts($products){
        $productOption = JSFactory::getTable('productOption');
        $listrp = $productOption->getProductOptionList($products, 'return_policy');
        $listrp = array_unique($listrp);
        $tmp = $this->loadData('return_policy');
        $defidrp = intval($tmp->id);
        foreach($listrp as $k=>$v){
            if (!$v) $listrp[$k] = $defidrp;
        }
        return $this->loadDataByIds($listrp);
    }
    
}