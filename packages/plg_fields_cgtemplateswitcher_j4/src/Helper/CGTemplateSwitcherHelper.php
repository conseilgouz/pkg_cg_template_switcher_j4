<?php
/*
* CG template switcher Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
namespace ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Helper;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;

class CGTemplateSwitcherHelper
{
	static function getTemplates($params)
	{
		if ($params->getAttribute('templatesall','true') == 'true') { // all the templates
			$styles = self::getListStyles();
		} else { // selected templates
			$list = $params->getAttribute('templates',array());
			$styles = self::getListStyles($list);
		}
		$results             = new \stdClass();
        $results->imagewidth ='150'; // default image width
		$results->options    = array();
		foreach ($styles as $template) {
		    $template_dir = strtolower(JPATH_ROOT.'/templates/'.$template->template); // for file_exists
		    $template_dir_html =  Uri::root().'/templates/'. $template->template; // for display
		    $template_media_dir = strtolower(JPATH_ROOT.'/media/templates/site/'.$template->template.'/images');
			$template_media_dir_html = Uri::root().'/media/templates/site/'.$template->template.'/images';
			$results->options[$template->id] = HTMLHelper::_('select.option', $template->id, $template->title);
			$results->home[$template->id] = $template->home;
		}
		return $results;
	}
	static function getListStyles($list = null )
	{
		$clientId = 0;
		// Create a new query object.
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->createQuery();
		// Select the required fields from the table.
		$query->select(
				'a.id, a.template, a.title, a.home, a.client_id, l.title AS language_title, l.image as image, l.sef AS language_sef'
		);
		$query->from($db->quoteName('#__template_styles', 'a'))
			->where($db->quoteName('a.client_id') . ' = ' . $clientId);
		// Join over the language.
		$query->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.home'));
		// Filter by extension enabled.
		$query->select($db->quoteName('extension_id', 'e_id'))
			->join('LEFT', $db->quoteName('#__extensions', 'e') . ' ON e.element = a.template AND e.client_id = a.client_id')
			->where($db->quoteName('e.enabled') . ' = 1')
			->where($db->quoteName('e.type') . ' = ' . $db->quote('template'));
		if ($list > 0) {
		    $query->where($db->quotename('a.id').' in ('.$list.')' );
		}
		// Add the list ordering clause.
		$query->order($db->escape('a.title') . ' ' . $db->escape( 'ASC'));
		$db->setQuery($query);
		$items= $db->loadObjectList();
		return $items;
	}
}
?>