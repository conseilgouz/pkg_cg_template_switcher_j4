<?php
/**
 * @package CG template switcher Module
 * @version 2.0.0
 * @subpackage  system.cg_style
 *
 * @copyright   Copyright (C) 2021 Conseilgouz. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
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
			$query->select('template, params');
			$query->from('#__template_styles');
			$query->where('client_id = 0');
			$query->where('id = ' . (int)$cookieValue);
			$db->setQuery($query);
			$style = $db->loadObject();
			if ($style != null) {
				$app->setTemplate( $style->template, $style->params );
			}
		}
    }

}
