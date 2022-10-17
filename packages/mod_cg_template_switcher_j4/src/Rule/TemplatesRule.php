<?php
/**
* CG template switcher Module
* Version			: 2.0.6
* copyright 		: Copyright (C) 2022 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
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