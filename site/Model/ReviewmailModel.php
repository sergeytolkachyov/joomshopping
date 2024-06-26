<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class ReviewMailModel  extends MailModel{
	
    public function getSubjectMail(){
		$subject = Text::_('JSHOP_NEW_COMMENT');
		extract(Helper::Js_add_trigger(get_defined_vars(), "before"));
        return $subject;
    }
    
    public function getMessageMail(){
		$product = JSFactory::getTable('product');
        $product->load($this->getProductId());
		$review = $this->getReview();
		
        $view = $this->getView("product");
        $view->setLayout("commentemail");
        $view->set('product_name', $product->getName());
        $view->set('user_name', $review->user_name);
        $view->set('user_email', $review->user_email);
        $view->set('mark', $review->mark);
        $view->set('review', $review->review);
		extract(Helper::Js_add_trigger(get_defined_vars(), "before"));
        return $view->loadTemplate();
    }
    
    public function send(){
		$mainframe =Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
		
		$mailfrom = $mainframe->getCfg('mailfrom');
        $fromname = $mainframe->getCfg('fromname');
		
        try {
            $mailer = Factory::getMailer();
            $mailer->setSender(array($mailfrom, $fromname));
            $mailer->addRecipient($jshopConfig->getAdminContactEmails());
            $mailer->setSubject($this->getSubjectMail());
            $mailer->setBody($this->getMessageMail());
            $mailer->isHTML(true);
            extract(Helper::Js_add_trigger(get_defined_vars(), "before"));
            $res = $mailer->Send();
        } catch (\Exception $e) {
            $res = 0;
            Helper::saveToLog('error.log', 'Reviewmail mail send error: '.$e->getMessage());			
        }
        return $res;
    }
	
	protected function getReview(){
		return $this->data['review'];
	}
    
    protected function getProductId(){
		return (int)$this->data['product_id'];
	}
    
}