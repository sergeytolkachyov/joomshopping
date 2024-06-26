<?php
/**
* @version      5.4.0 09.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die();

class ShippingsController extends BaseadminController{
    
    protected $urlEditParamId = 'shipping_id';

    function init(){
        HelperAdmin::checkAccessController("shippings");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.shippings";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

		$shippings = JSFactory::getModel("shippings");
		$rows = $shippings->getAllShippings(0, $filter_order, $filter_order_Dir);

        $not_set_price = array();
        $rowsprices = $shippings->getAllShippingPrices(0);
        $shippings_prices = array();
        foreach($rowsprices as $row){
            $shippings_prices[$row->shipping_method_id][] = $row;
        }
        foreach($rows as $k=>$v){
            if (isset($shippings_prices[$v->shipping_id])){
                $rows[$k]->count_shipping_price = count($shippings_prices[$v->shipping_id]);
            }else{
				$not_set_price[] = '<a href="index.php?option=com_jshopping&controller=shippingsprices&task=edit&shipping_id_back='.$rows[$k]->shipping_id.'">'.$rows[$k]->name.'</a>';
                $rows[$k]->count_shipping_price = 0;
            }
        }

        foreach($rows as $k => $v) {
            $v->tmp_extra_column_cells = "";
        }

        if ($not_set_price){
            JSError::raiseNotice("", Text::_('JSHOP_CERTAIN_METHODS_DELIVERY_NOT_SET_PRICE').' ('.implode(', ',$not_set_price).')!');
        }

		$view = $this->getView("shippings", 'html');
        $view->setLayout("list");
		$view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->tmp_html_start = "";
        $view->tmp_extra_column_headers = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayShippings', array(&$view));
		$view->displayList();
	}

	function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
		$jshopConfig = JSFactory::getConfig();
		$shipping_id = $this->input->getInt("shipping_id");
		$shipping = JSFactory::getTable('shippingmethod');
		$shipping->load($shipping_id);
		$edit = ($shipping_id)?($edit = 1):($edit = 0);
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
		$params = $shipping->getParams();

        $active_payments = $shipping->getPayments();
        if (!count($active_payments)){
            $active_payments = array(0);
        }

        $lists['payments'] = HTMLHelper::_('select.genericlist', SelectOptions::getPayments(Text::_('JSHOP_ALL')), 'listpayments[]', 'class="inputbox form-select" size="10" multiple = "multiple"', 'payment_id', 'name', $active_payments);
        $lists['access'] = HTMLHelper::_('select.genericlist', SelectOptions::getAccessGroups(), 'access','class = "inputbox form-select"','id','title', $shipping->access);

        $nofilter = array();
        OutputFilter::objectHTMLSafe($shipping, ENT_QUOTES, $nofilter);

		$view = $this->getView("shippings", 'html');
        $view->setLayout("edit");
		$view->set('params', $params);
		$view->set('shipping', $shipping);
		$view->set('edit', $edit);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('lists', $lists);
		$view->set('config', $jshopConfig);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_after_image = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditShippings', array(&$view));
		$view->displayEdit();
	}

    function ext_price_calc(){
        $this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice");
    }

}