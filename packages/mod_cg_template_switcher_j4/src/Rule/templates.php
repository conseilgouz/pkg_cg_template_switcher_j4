<?php
/**
* CG template switcher Module
* Version			: 2.0.6
* Package			: Joomla 3.10.x
* copyright 		: Copyright (C) 2022 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
class JFormRuleTemplates extends JFormRule
{

	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null) {

		if (!JPluginHelper::isEnabled('system', 'cgstyle')) {
			JFactory::getApplication()->enqueueMessage(JText::_('CG_ACTIVATE'),'error');
			return false;
		}
        return true;

	}
}