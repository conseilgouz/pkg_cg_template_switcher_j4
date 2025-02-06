<?php
/*
* CG template switcher Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Extension;

defined('_JEXEC') or die;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;

/**
 * Fields Text Plugin
 *
 */
class Cgtemplateswitcher extends FieldsPlugin
{
    public function onCustomFieldsPrepareDom($field, \DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if (!$fieldNode) {
            return $fieldNode;
        }

        $fieldNode->setAttribute('templateall', $field->fieldparams->get('templateall', 'true'));

        FormHelper::addFieldPrefix('ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Form\Field');
        return $fieldNode;
    }
}
