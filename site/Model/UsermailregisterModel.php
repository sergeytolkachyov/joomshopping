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
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class UserMailRegisterModel  extends MailModel{
	
    private $registration_request_data;
    
    public function setRegistrationRequestData(&$post){
        $this->registration_request_data = &$post;
    }
    
    public function getSubjectMail(){
        $params = $this->getParams();
        $data = $this->getData();
        $useractivation = $params->get('useractivation');
        if ($useractivation == 2){
            $subject = Text::_('COM_USERS_EMAIL_ACCOUNT_DETAILS');
        }else if ($useractivation == 1){
            $subject = Text::_('COM_USERS_EMAIL_ACCOUNT_DETAILS');
        }else{
            $subject = Text::_('COM_USERS_EMAIL_ACCOUNT_DETAILS');
        }
        $search = ['{NAME}', '{SITENAME}'];
        $replace = [$data['name'], $data['sitename']];
        $subject = str_replace($search, $replace, $subject);
        return $subject;
    }
    
    public function getMessageMail(){
        $params = $this->getParams();
        $data = $this->getData();
        $useractivation = $params->get('useractivation');
        $sendpassword = $params->get('sendpassword', 1);
        if ($useractivation == 2){
            if ($sendpassword) {
				$emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY');
			} else {
				$emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW');
			}
        }else if ($useractivation == 1){
			if ($sendpassword) {
				$emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY');
			} else {
				$emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW');
			}
        } else {
            if ($sendpassword){
                $emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_BODY');
            }else{
                $emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_BODY_NOPW');
            }
        }
        
        $search = ['{NAME}', '{SITENAME}', '{SITEURL}', '{USERNAME}', '{ACTIVATE}', '{PASSWORD_CLEAR}'];
        $replace = [$data['name'], $data['sitename'], $data['siteurl'], $data['username'], $data['linkactivate'], $data['password_clear']];
        $emailBody = str_replace($search, $replace, $emailBody);

        $view = $this->getView('user');
        $view->setLayout("registermail");
        $view->set('data', $data);
        $view->set('useractivation', $useractivation);
        $view->set('sendpassword', $sendpassword);
        $view->set('emailBody', $emailBody);
        Factory::getApplication()->triggerEvent('onBeforeRegistermailView', array(&$view));
        return $view->loadTemplate();
    }
    
    public function send(){
        $dispatcher = Factory::getApplication();        
        $emailSubject = $this->getSubjectMail();
        $emailBody = $this->getMessageMail();
        $data = $this->getData();
        $dispatcher->triggerEvent('onBeforeRegisterSendMailClient', array(&$this->registration_request_data, &$data, &$emailSubject, &$emailBody));
        try{
            $mailer = Factory::getMailer();
            $mailer->setSender(array($data['mailfrom'], $data['fromname']));
            $mailer->addRecipient($data['email']);
            $mailer->setSubject($emailSubject);
            $mailer->setBody($emailBody);
            $mailer->isHTML(JSFactory::getConfig()->register_mail_html_format);
            $dispatcher->triggerEvent('onBeforeRegisterMailerSendMailClient', array(&$mailer, &$this->registration_request_data, &$data, &$emailSubject, &$emailBody));
            $mailer->Send();
        } catch (\Exception $e) {
            Helper::saveToLog('error.log', 'Usermailregister mail send error: '.$e->getMessage());			
        }
    }
    
    public function getSubjectMailAdmin(){
        $data = $this->getData();
        $subject = Text::_('COM_USERS_EMAIL_ACCOUNT_DETAILS');
        $search = ['{NAME}', '{SITENAME}'];
        $replace = [$data['name'], $data['sitename']];
        $subject = str_replace($search, $replace, $subject);
        return $subject;
    }
    
    public function getMessageMailAdmin(){
        $data = $this->getData();
        $emailBody = Text::_('COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY');
        $search = ['{NAME}', '{SITENAME}', '{SITEURL}', '{USERNAME}'];
        $replace = [$data['name'], $data['sitename'], $data['siteurl'], $data['username']];
        $emailBody = str_replace($search, $replace, $emailBody);
        
        $view = $this->getView('user');
        $view->setLayout("registermailadmin");
        $view->set('data', $data);
        $view->set('emailBody', $emailBody);
        Factory::getApplication()->triggerEvent('onBeforeRegistermailAdminView', array(&$view));
        return $view->loadTemplate();
    }
    
    public function sendToAdmin(){
        $dispatcher = Factory::getApplication();
        $data = $this->getData();
        $emailSubject = $this->getSubjectMailAdmin();
        $emailBodyAdmin = $this->getMessageMailAdmin();
        $rows = $this->getListAdminUserSendEmail();        
        $mode = JSFactory::getConfig()->register_mail_admin_html_format;
        foreach($rows as $row){
            $dispatcher->triggerEvent('onBeforeRegisterSendMailAdmin', array(&$this->registration_request_data, &$data, &$emailSubject, &$emailBodyAdmin, &$row, &$mode));
            try { 
                Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin, $mode);
            } catch (\Exception $e) {   
                Helper::saveToLog('error.log', 'Usermailregister mail send error: '.$e->getMessage());			
            }
        }
    }
    
}