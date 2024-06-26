<?php
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
 * @version      5.0.0 15.09.2018
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();

$countprod = count($this->products);

?>
<div class="jshop" id="comjshop">

<?php if ($countprod > 0) : ?>
    <table class="jshop cart cartwishlist" id="comjshop">
        <tr>
            <th class="jshop_img_description_center">
                <?php print Text::_('JSHOP_IMAGE')?>
            </th>
            <th class="product_name">
                <?php print Text::_('JSHOP_ITEM')?>
            </th>
            <th class="single_price">
                <?php print Text::_('JSHOP_SINGLEPRICE')?>
            </th>
            <th class="remove_to_cart">
            </th>
        </tr>
        <?php
        $i=1;
        foreach($this->products as $key_id=>$prod){
            echo $prod['_tmp_tr_before'];
        ?>
        <tr class="jshop_prod_cart <?php if ($i%2==0) print "even"; else print "odd"?>">
            <td class="jshop_img_description_center">
                <div class="data">
                    <?php echo $prod['_tmp_img_before']; ?>
                    <a class="prodname" href="<?php print $prod['href']; ?>">
                        <img src="<?php print $this->image_product_path ?>/<?php if ($prod['thumb_image']) print $prod['thumb_image']; else print $this->no_image; ?>" alt="<?php print htmlspecialchars($prod['product_name']);?>" class="jshop_img" />
                    </a>
                    <?php echo $prod['_tmp_img_after']; ?>
                </div>
            </td>
            <td class="product_name">
                <div class="data">
                    <a href="<?php print $prod['href']?>">
                        <?php print $prod['product_name']?>
                    </a>
                    <?php if ($this->config->show_product_code_in_cart){?>
                        <span class="jshop_code_prod">(<?php print $prod['ean']?>)</span>
                    <?php }?>
    				<?php print $prod['_ext_product_name'] ?>
                    <?php if ($prod['manufacturer']!=''){?>
                        <div class="manufacturer">
                            <?php print Text::_('JSHOP_MANUFACTURER')?>:
                            <span><?php print $prod['manufacturer']?></span>
                        </div>
                    <?php }?>
                    <?php if ($this->config->manufacturer_code_in_cart && $prod['manufacturer_code']){?>
                        <div class="manufacturer_code"><?php print Text::_('JSHOP_MANUFACTURER_CODE')?>: <span><?php print $prod['manufacturer_code'] ?></span></div>
                    <?php }?>
                    <?php if ($this->config->real_ean_in_cart && $prod['real_ean']){?>
                        <div class="real_ean"><?php print Text::_('JSHOP_EAN')?>: <span><?php print $prod['real_ean'] ?></span></div>
                    <?php }?>
                    <?php print Helper::sprintAtributeInCart($prod['attributes_value']);?>
                    <?php print Helper::sprintFreeAtributeInCart($prod['free_attributes_value']);?>
                    <?php print Helper::sprintFreeExtraFiledsInCart($prod['extra_fields']);?>
                    <?php print $prod['_ext_attribute_html']?>
                </div>
            </td>
            <td class="single_price">
                <div class="data">
                    <span class="price">
    					<?php print Helper::formatprice($prod['price'])?>
    				</span>
                    <?php print $prod['_ext_price_html']?>
                    <?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
                        <span class="taxinfo"><?php print Helper::productTaxInfo($prod['tax']);?></span>
                    <?php }?>
                    <?php if ($this->config->cart_basic_price_show && $prod['basicprice']>0){?>
                        <div class="basic_price">
                            <?php print Text::_('JSHOP_BASIC_PRICE')?>: <span><?php print Helper::sprintBasicPrice($prod);?></span>
                        </div>
                    <?php }?>
                </div>
            </td>
            <td class="remove_to_cart">
                <div class="data">
                    <a class="btn btn-success" href="<?php print $prod['remove_to_cart'] ?>" >
                        <?php print Text::_('JSHOP_REMOVE_TO_CART')?>
                    </a>
                    <a class="button-img btn-danger btn" href="<?php print $prod['href_delete']?>" onclick="return confirm('<?php print Text::_('JSHOP_CONFIRM_REMOVE')?>')">
                        <?php print Text::_('JSHOP_DELETE')?>
                    </a>
                </div>
            </td>
        </tr>
        <?php
            echo $prod['_tmp_tr_after'];
            $i++;
        }
        ?>
    </table>
<?php else : ?>
    <div class="wishlist_empty_text"><?php print Text::_('JSHOP_WISHLIST_EMPTY') ?></div>
<?php endif; ?>

<?php print $this->_tmp_html_before_buttons?>

<div class="jshop wishlish_buttons">
    <div id="checkout" class="d-flex justify-content-between">

        <div class="pull-left">
            <a href="<?php print $this->href_shop ?>" class="btn btn-arrow-left btn-secondary">
                <?php print Text::_('JSHOP_BACK_TO_SHOP')?>
            </a>
        </div>

        <div class="pull-right">
            <a href="<?php print $this->href_checkout ?>" class="btn btn-arrow-right btn-secondary">
                <?php print Text::_('JSHOP_GO_TO_CART')?>
            </a>
        </div>
    </div>
</div>

<?php print $this->_tmp_html_after_buttons?>

</div>