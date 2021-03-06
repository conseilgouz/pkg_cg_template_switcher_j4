<?php
/**
 * @package CG template switcher Module
 * @version 2.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2021 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 */
namespace ConseilGouz\Module\CGTemplateSwitcher\Site\Helper;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

class CGTemplateSwitcherHelper
{
	static function getTemplates($params)
	{
		
		if ($params->get('templatesall','true') == 'true') { // all the templates
			$styles = self::getListStyles();
		} else { // selected templates
			$list = $params->get('templates',array());
			$styles = self::getListStyles($list);
		}
		
		$results             = new \stdClass();
        $results->imagewidth ='150'; // default image width
		$results->options    = array();
		foreach ($styles as $template) {
		    $template_dir = strtolower(JPATH_ROOT.'/templates/'.$template->template);
			$results->options[$template->id] = HTMLHelper::_('select.option', $template->id, $template->title);
			$results->home[$template->id] = $template->home;
			if ($params->get('showpreview', 'true') == 'true') {
			    if (file_exists($template_dir.'/template_thumbnail.png')) {
				    $results->images[$template->id]         = new \stdClass();
				    $results->images[$template->id]->name   = $template->title;
				    $results->images[$template->id]->src    = \JURI::root().'/templates/'.$template->template.'/template_thumbnail.png';
				    $results->images[$template->id]->width  = '150';
				    $results->images[$template->id]->height = '100';
				    $results->images[$template->id]->preview = \JURI::root().'/templates/'.$template->template.'/template_thumbnail.png';;
				    if (file_exists($template_dir.'/template_preview.png')) {
				        $results->images[$template->id]->preview    = \JURI::root().'/templates/'.$template->template.'/template_preview.png';
				    }
			   }
				
			}

		}
		return $results;
	}
	
	static function getListStyles($list = array() )
	{
		$clientId = 0;
		// Create a new query object.
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
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
		if (count($list) > 0) {
		    $query->where($db->quotename('a.id').' in ('.implode(",",$list).')' );
		}
		// Add the list ordering clause.
		$query->order($db->escape('a.title') . ' ' . $db->escape( 'ASC'));
		$db->setQuery($query);
		$items= $db->loadObjectList();
		return $items;
	}
// AJAX Request 	
	public static function getAjax() {
        $input = Factory::getApplication()->input;
		$params = new Registry($module->params);  		
        $output = '';
		if ($input->get('data') == "param") {
			return self::getParams($params);
		}
		return false;
	}
	private static function getParams($params) {

		$templates =  self::getTemplates($params);
		$templates_js = array();
		if ($params->get('showpreview', 'true') == 'true') {		
			foreach ($templates->images as $template => $image) { 
			$arr['width'] =  $image->width;
			$arr['height'] = $image->height;
			$arr['src'] = $image->src;
			$arr['preview'] = $image->preview;
			$templates_js[$template] = $arr;
			}
		}
		$ret = '{"cookie_duration":"'.$params->get('cookie_duration', 0).'","showpreview":"'.$params->get('showpreview', 'true').'","autoswitch":"'.$params->get('autoswitch', 'false').'","noimage":"'. JText::_('NOIMAGE').'","templates":'.json_encode($templates_js).'}';
		return $ret;
	}
}

?>