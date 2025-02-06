<?php
/**
* CG template switcher Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Plugin\Field\Cgtemplateswitcher\Rule;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;

class TemplatesRule extends FormRule
{
    public function test(\SimpleXMLElement $element, $value, $group = null, ?Registry $input = null, ?Form $form = null)
    {

        if (!PluginHelper::isEnabled('system', 'cgstyle')) {
            Factory::getApplication()->enqueueMessage(Text::_('CG_ACTIVATE'), 'error');
            return false;
        }
        return true;

    }
}
