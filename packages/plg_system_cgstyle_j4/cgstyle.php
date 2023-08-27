<?php
/**
 * @package CG template switcher Module
 * @version 2.1.0
 * @subpackage  system.cg_style
 *
 * @copyright   Copyright (C) 2023 Conseilgouz. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Version;

class PlgSystemCGStyle extends CMSPlugin
{
	public function onAfterRoute() {
		$app= Factory::getApplication();
		if ($app->isClient('administrator')) { // run only on frontend
			return;
		}
		$cookieValue = Factory::getApplication()->input->cookie->get('cg_template');	
		if ($cookieValue) {
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__template_styles');
			$query->where('client_id = 0');
			$query->where('id = ' . (int)$cookieValue);
			$db->setQuery($query);
			$style = $db->loadObject();
			if ($style != null) {
				$j = new Version();
				$version=substr($j->getShortVersion(), 0,1); 
				if ($version >= "4") { // Joomla 4 and higher
					$app->setTemplate( $style);
				}else { //  Joomla 3.10
					$app->setTemplate( $style->template, $style->params );
				}
			}
		}
    }

}
