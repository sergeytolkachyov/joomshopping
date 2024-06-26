<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
HTMLHelper::_('bootstrap.tooltip');
$lists=$this->lists;
$jshopConfig=$this->config;
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuConfigs('otherconfig');?>
<div class="jshop_edit"> 
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="10">

<div class="card">
<h3 class="card-header bg-primary text-white"><?php echo Text::_('JSHOP_OC')?></h3>
<div class="card-body">
<table class="admintable table-striped">
<tr>
    <td class="key" style="width:220px">
        <?php echo Text::_('JSHOP_EXTENDED_TAX_RULE_FOR')?>
    </td>
    <td>
        <?php print $lists['tax_rule_for'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo Text::_('JSHOP_SAVE_ALIAS_AUTOMATICAL')?>
    </td>
    <td>
        <input type="hidden" name="create_alias_product_category_auto" value="0">
        <input type="checkbox" name="create_alias_product_category_auto" value="1" <?php if ($jshopConfig->create_alias_product_category_auto) echo 'checked="checked"';?> />
    </td>
</tr>

<?php foreach($this->other_config as $k){    
?>
<tr>
	<td class="key">		
        <?php print Text::_("JSHOP_OC_".$k)?>
	</td>
	<td>
        <?php if (in_array($k, $this->other_config_checkbox)){?>
            <input type="hidden" name="<?php print $k?>" value="0">
            <input type="checkbox" name="<?php print $k?>" value="1" <?php if ($jshopConfig->$k==1) print 'checked'?>>
        <?php }elseif (isset($this->other_config_select[$k])){?>
            <?php 
            $option = array();
            foreach($this->other_config_select[$k] as $k2=>$v2){
                $option_name = $v2;
                if (Text::_("JSHOP_OC_".$k."_".$v2)!="JSHOP_OC_".$k."_".$v2){
                    $option_name = Text::_("JSHOP_OC_".$k."_".$v2);
                }
                $option[] = HTMLHelper::_('select.option', $k2, $option_name, 'id', 'name');
            }
            print HTMLHelper::_('select.genericlist', $option, $k, 'class = "inputbox form-select"', 'id', 'name', $jshopConfig->$k);
            ?>
        <?php }else{?>
		    <input type="text" name="<?php print $k?>" class = "form-control" value="<?php echo $jshopConfig->$k?>">
        <?php }?>
        
		<?php if (Text::_("JSHOP_OC_".$k."_INFO")!="JSHOP_OC_".$k."_INFO") echo HelperAdmin::tooltip(Text::_("JSHOP_OC_".$k."_INFO"));?>
	</td>
</tr>
<?php } ?>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</div>
</div>
<?php print $this->tmp_html_end?>
</form>
</div>
</div>