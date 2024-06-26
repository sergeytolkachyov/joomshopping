<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class ProductAjaxRequestModel  extends BaseModel{
	
	public $product;
	public $product_id;
	public $change_attr;
	public $qty;
	public $attribs;
	public $freeattr;
	public $request;
    public $displayonlyattrtype = null;
	
	public function __construct(){
		$this->product = JSFactory::getTable('product');
	}	
	
	public function setData(&$product_id, &$change_attr, &$qty, &$attribs, &$freeattr, &$request){		
        Factory::getApplication()->triggerEvent('onBeforeLoadDisplayAjaxAttrib', array(&$product_id, &$change_attr, &$qty, &$attribs, &$freeattr, &$request));
		$this->product_id = $product_id;
		$this->change_attr = $change_attr;
		$this->qty = $qty;
		$this->attribs = $attribs;
		$this->freeattr = $freeattr;
		$this->request = $request;
	}
	
	public function getProduct(){
		return $this->product;
	}
	
	public function getProductId(){
		return $this->product_id;
	}
	
	public function getChangeAttr(){
		return $this->change_attr;
	}
	
	public function getQty(){
		return $this->qty;
	}
	
	public function getAttribs(){
		return $this->attribs;
	}
	
	public function getFreeattr(){
		return $this->freeattr;
	}
	
	public function getRequest(){
		return $this->request;
	}
	
	public function getLoadProductData(){
		$jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
		
		$product = $this->product;
		$product->load($this->product_id);
		$dispatcher->triggerEvent('onBeforeLoadDisplayAjaxAttrib2', array(&$product));
		
		$attributes = $product->getInitLoadAttribute($this->attribs, $this->displayonlyattrtype);
        $product->setFreeAttributeActive($this->freeattr);

        $rows = array();
        foreach($attributes as $k=>$v){
            $rows['id_'.$k] = $v->selects;
        }

        $pricefloat = $product->getPrice($this->qty, 1, 1, 1);
        $price = Helper::formatprice($pricefloat);
        $available = intval($product->getQty() > 0);
		$displaybuttons = intval(intval($product->getQty() > 0) || $jshopConfig->hide_buy_not_avaible_stock==0);
        $ean = $product->getEan();
        $manufacturer_code = $product->getManufacturerCode();
        $real_ean = $product->getRealEan();
        $weight = Helper::formatweight($product->getWeight());
        $basicprice = Helper::formatprice($product->getBasicPrice());
        
        $rows['price'] = $price;
        $rows['pricefloat'] = $pricefloat;
        $rows['available'] = $available;
        $rows['ean'] = $ean;
        $rows['manufacturer_code'] = $manufacturer_code;
        $rows['real_ean'] = $real_ean;
        if ($jshopConfig->admin_show_product_basic_price){
            $rows['basicprice'] = $basicprice;
        }
        if ($jshopConfig->product_show_weight){
            $rows['weight'] = $weight;
        }
        if ($jshopConfig->product_list_show_price_default && isset($product->product_price_default) && $product->product_price_default>0){
            $rows['pricedefault'] = Helper::formatprice($product->product_price_default);
        }
        if ($jshopConfig->product_show_qty_stock){
            $qty_in_stock = Helper::getDataProductQtyInStock($product);
            $rows['qty'] = Helper::sprintQtyInStock($qty_in_stock);
        }
		
        $product->updateOtherPricesIncludeAllFactors();

        if (is_array($product->product_add_prices)){
            foreach($product->product_add_prices as $k=>$v){
                $rows['pq_'.$v->product_quantity_start] = Helper::formatprice($v->price).(isset($v->ext_price) ? $v->ext_price : '');
                if (isset($v->basic_price)) {
                    $rows['pqb_'.$v->product_quantity_start] = Helper::formatprice($v->basic_price);
                }
            }
        }
        if ($product->product_old_price){
            $old_price = Helper::formatprice($product->product_old_price);
            $rows['oldprice'] = $old_price;
        }
		$rows['displaybuttons'] = $displaybuttons;
        if ($jshopConfig->hide_delivery_time_out_of_stock){
            $rows['showdeliverytime'] = $product->getDeliveryTimeId();
        }
        
        if ($jshopConfig->use_extend_attribute_data){
            $template_path = $jshopConfig->template_path.$jshopConfig->template."/product";
            $images = $product->getImages();
            $videos = $product->getVideos();
			$demofiles = $product->getDemoFiles();
			if (!file_exists($template_path."/block_image_thumb.php")){
				$tmp = array();
				foreach($images as $img){
					$tmp[] = $img->image_name;
				}
				$displayimgthumb = intval((count($images)>1) || (count($videos) && count($images)));
				$rows['images'] = $tmp;
				$rows['displayimgthumb'] = $displayimgthumb;
            }

			$view = $this->getView("product");
			$view->setLayout("demofiles");
			$view->set('config', $jshopConfig);
			$view->set('demofiles', $demofiles);
            $demofiles = $view->loadTemplate();            
            $rows['demofiles'] = $demofiles;

			if (file_exists($template_path."/block_image_thumb.php")){
                $product->getDescription();
                
                $view = $this->getView("product");
                $view->setLayout("block_image_thumb");
                $view->set('config', $jshopConfig);            
                $view->set('images', $images);            
                $view->set('videos', $videos);            
                $view->set('image_product_path', $jshopConfig->image_product_live_path);            
                $dispatcher->triggerEvent('onBeforeDisplayProductViewBlockImageThumb', array(&$view));
                $block_image_thumb = $view->loadTemplate();
                
                $view = $this->getView("product");
                $view->setLayout("block_image_middle");
                $view->set('config', $jshopConfig);            
                $view->set('images', $images);            
                $view->set('videos', $videos);            
                $view->set('product', $product);            
                $view->set('noimage', $jshopConfig->noimage);            
                $view->set('image_product_path', $jshopConfig->image_product_live_path);
                $view->set('path_to_image', $jshopConfig->live_path.'images/');
                $view->_tmp_product_html_body_image = "";
                $dispatcher->triggerEvent('onBeforeDisplayProductViewBlockImageMiddle', array(&$view));
                $block_image_middle = $view->loadTemplate();

                $rows['block_image_thumb'] = $block_image_thumb;

                $rows['block_image_middle'] = $block_image_middle;
            }
        }
        $obj = $this;
		$dispatcher->triggerEvent('onBeforeDisplayAjaxAttribRows', array(&$rows, &$obj));
        return $rows;
	}
	
	public function getProductDataJson(){        
		$prod_data = $this->getLoadProductData();
        Factory::getApplication()->triggerEvent('onBeforeDisplayAjaxAttrib', array(&$prod_data, &$this->product) );
        return json_encode($prod_data);		
	}
	
}