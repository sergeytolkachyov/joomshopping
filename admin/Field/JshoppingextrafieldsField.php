<?php
/**
 * @version      5.5.0 07.06.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

class JshoppingextrafieldsField extends ListField
{
	protected $type = 'jshoppingextrafields';

	protected function getOptions(): array
	{
		require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		$productfield = JSFactory::getTable('productfield');
        $list = $productfield->getList();
		$options = [];
		$default = $this->element['default'] ?? null;
        if (isset($default) && ((string)$default) === '') {
            $options[] = HTMLHelper::_('select.option', '', '');
        }
		foreach ($list as $item) {
			$options[] = HTMLHelper::_('select.option', $item->id, $item->name);
		}

		return $options;
	}
}