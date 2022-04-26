<?php
/**
* CG template switcher Module
* Version			: 1.0.10
* Package			: Joomla 3.7.x
* copyright 		: Copyright (C) 2018 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Updated on        : March, 2018
* Version 01.0.10   : CSP Compliance
*/
namespace ConseilGouz\Module\CGTemplateSwitcher\Site\Rule;
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;

class TemplatesRule extends FormRule
{

	public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null) {

		if (!PluginHelper::isEnabled('system', 'cgstyle')) {
			Factory::getApplication()->enqueueMessage(Text::_('CG_ACTIVATE'),'error');
			return false;
		}
        return true;

	}
}