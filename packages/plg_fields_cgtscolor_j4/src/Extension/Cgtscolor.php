<?php
/*
* CG template switcher color mode Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Plugin\Fields\Cgtscolor\Extension;

defined('_JEXEC') or die;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;

/**
 * Fields Text Plugin
 *
 */
class Cgtscolor extends FieldsPlugin
{
    public function onCustomFieldsPrepareDom($field, \DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if (!$fieldNode) {
            return $fieldNode;
        }

        FormHelper::addFieldPrefix('ConseilGouz\Plugin\Fields\Cgtscolor\Form\Field');
        return $fieldNode;
    }
}
