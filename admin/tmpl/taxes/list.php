<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows = $this->rows;
$i = 0;
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=taxes" method="post" name="adminForm" id="adminForm">

<?php print $this->tmp_html_start?>

<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'tax_name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="200">
        <?php echo Text::_('JSHOP_EXTENDED_RULE_TAX')?>
    </th>
    <th width="50" class="center">
        <?php echo Text::_('JSHOP_EDIT')?>
    </th>
    <th width="40" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'tax_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){ ?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo HTMLHelper::_('grid.id', $i, $row->tax_id);?>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=taxes&task=edit&tax_id=<?php echo $row->tax_id; ?>"><?php echo $row->tax_name;?></a> (<?php echo $row->tax_value;?> %)
   </td>
   <td>
    <a class="btn btn-mini btn-info" href="index.php?option=com_jshopping&controller=exttaxes&back_tax_id=<?php echo $row->tax_id; ?>">
        <?php echo Text::_('JSHOP_EXTENDED_RULE_TAX')?>
    </a>
   </td>
   <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=taxes&task=edit&tax_id=<?php echo $row->tax_id; ?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
        <?php print $row->tax_id;?>
   </td>
  </tr>
<?php
$i++;
}
?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="<?php echo Factory::getApplication()->input->getVar('task')?>" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>