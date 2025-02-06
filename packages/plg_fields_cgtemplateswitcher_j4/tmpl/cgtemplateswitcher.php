<?php
/*
* CG template switcher Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
defined('_JEXEC') or die;
use ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Helper\CGTemplateSwitcherHelper;

$value = $field->value;

$template =  CGTemplateSwitcherHelper::getListStyles($value);

if ($value == '') {
    return;
}
echo $template[0]->title;
