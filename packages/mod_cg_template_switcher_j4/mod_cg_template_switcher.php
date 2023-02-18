<?php
/**
 * @package CG template switcher Module
 * @version 2.0.5 
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use ConseilGouz\Module\CGTemplateSwitcher\Site\Helper\CGTemplateSwitcherHelper;

JLoader::registerNamespace('ConseilGouz\Module\CGTemplateSwitcher\Site', JPATH_SITE . '/modules/mod_cg_template_switcher/src', false, false, 'psr4');

if (!PluginHelper::isEnabled('system', 'cgstyle')) {
	Factory::getApplication()->enqueueMessage(Text::_('CG_ACTIVATE'),'error');
	return false;
}
$document 		= Factory::getDocument();
$modulefield	= ''.JURI::base(true).'/media/mod_cg_template_switcher/';

$document->addStyleDeclaration($params->get('css','')); 

$templates =  CGTemplateSwitcherHelper::getTemplates($params);

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
$document->addScriptOptions('mod_cg_template_switcher', 
	array('cookie_duration' => $params->get('cookie_duration', 0),'showpreview' => $params->get('showpreview', 'true'),
		  'autoswitch' => $params->get('autoswitch', 'false'),
		  'noimage' => Text::_('NOIMAGE'),'templates' => $templates_js));
$document->addScript($modulefield .'js/init.js');

require_once(ModuleHelper::getLayoutPath($module->module));

?>