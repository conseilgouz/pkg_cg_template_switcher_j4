<?php
/*
* CG template switcher color Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
defined('_JEXEC') or die;

$value = $field->value;

if ($value == '') {
    return;
}
echo $value;
