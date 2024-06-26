<?php
/**
* @version      5.1.0 15.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
defined( '_JEXEC' ) or die();

class ProductimagesController extends BaseadminController{
    
    function display($cachable = false, $urlparams = false){
		$jshopConfig = JSFactory::getConfig();        
		$filter = $this->input->getVar('filter', '');
		$path_length = strlen($jshopConfig->image_product_path) + 1;
        $files = [];
		foreach( new \RecursiveIteratorIterator ( new \RecursiveDirectoryIterator ( $jshopConfig->image_product_path ), \RecursiveIteratorIterator::SELF_FIRST ) as $v ) {
			$filename = substr($v, $path_length);            
            if ($filter!='' && !substr_count($filename, $filter)) continue;
			if (file_exists($jshopConfig->image_product_path .'/'.'thumb_'.$filename)){
                $files[] = $filename;
			}
		}		
        $view = $this->getView("productimages", 'html');
        $view->setLayout("list");
        $view->set('list', $files);
        $view->set('config', $jshopConfig);
        $view->set('filter', $filter);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductsImages', array(&$view));
		$view->displayList();
	}
}