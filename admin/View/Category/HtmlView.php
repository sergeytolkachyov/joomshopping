<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Category;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function displayList($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_TREE_CATEGORY'), 'generic.png' );
        \JToolBarHelper::addNew();
        \JToolBarHelper::publishList();
        \JToolBarHelper::unpublishList();
        \JToolBarHelper::deleteList(\JText::_('JSHOP_DELETE')."?");
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
	}
    function displayEdit($tpl=null){
        \JToolBarHelper::title( ($this->category->category_id) ? (\JText::_('JSHOP_EDIT_CATEGORY').' / '.$this->category->{\JSFactory::getLang()->get('name')}) : (\JText::_('JSHOP_NEW_CATEGORY')), 'generic.png' );
        \JToolBarHelper::save();
        \JToolBarHelper::apply();
        \JToolBarHelper::save2new();
        \JToolBarHelper::cancel();
        parent::display($tpl);
    }
}