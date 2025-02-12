<?php
/**
 * @package CG template switcher Module
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
 */

namespace ConseilGouz\Module\CGTemplateSwitcher\Site\Helper;

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Component\Fields\Administrator\Model\FieldModel;
use Joomla\Database\DatabaseInterface;

class CGTemplateSwitcherHelper
{
    public static function getTemplates($params)
    {
        if ($params->get('templatesall', 'true') == 'true') { // all the templates
            $styles = self::getListStyles();
        } else { // selected templates
            $list = $params->get('templates', array());
            $styles = self::getListStyles($list);
        }
        $results             = new \stdClass();
        $results->imagewidth = '150'; // default image width
        $results->options    = array();
        foreach ($styles as $template) {
            $template_dir = strtolower(JPATH_ROOT.'/templates/'.$template->template); // for file_exists
            $template_dir_html =  Uri::root().'/templates/'. $template->template; // for display
            $template_media_dir = strtolower(JPATH_ROOT.'/media/templates/site/'.$template->template.'/images');
            $template_media_dir_html = Uri::root().'/media/templates/site/'.$template->template.'/images';
            $results->options[$template->id] = HTMLHelper::_('select.option', $template->id, $template->title);
            $results->home[$template->id] = $template->home;
            if ($params->get('showpreview', 'true') == 'true') {
                $img = "";
                $img_preview = "";
                if (file_exists($template_dir.'/template_thumbnail.png')) {
                    $img = $template_dir_html.'/template_thumbnail.png';
                } elseif (file_exists($template_media_dir.'/template_thumbnail.png')) {
                    $img = $template_media_dir_html.'/template_thumbnail.png';
                }
                if (file_exists($template_dir.'/template_preview.png')) {
                    $img_preview = $template_dir_html.'/template_preview.png';
                }
                if (file_exists($template_media_dir.'/template_preview.png')) {
                    $img_preview = $template_media_dir_html.'/template_preview.png';
                }
                if ($img) {
                    $results->images[$template->id]         = new \stdClass();
                    $results->images[$template->id]->name   = $template->title;
                    $results->images[$template->id]->src    = $img;
                    $results->images[$template->id]->width  = '150';
                    $results->images[$template->id]->height = '100';
                    $results->images[$template->id]->preview = $img_preview;
                }
            }
        }
        return $results;
    }
    public static function getListStyles($list = array())
    {
        $clientId = 0;
        // Create a new query object.
        $db = Factory::getContainer()->get(DatabaseInterface::class);
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
            $query->where($db->quotename('a.id').' in ('.implode(",", $list).')');
        }
        // Add the list ordering clause.
        $query->order($db->escape('a.title') . ' ' . $db->escape('ASC'));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
    // ==============================================    AJAX Request 	============================================================
    public static function getAjax()
    {
        $input = Factory::getApplication()->input->request;
        $userid = $input->getInt('user');
        $tmpl = $input->getInt('tmpl');
        $color = $input->getInt('color');
        $user = Factory::getApplication()->getIdentity($userid);
        $test = FieldsHelper::getFields('com_users.user', $user);
        $template_id = 0;
        $field_id = 0;
        $color_id = 0;
        foreach ($test as $field) {
            if ($field->type == 'cgtemplateswitcher') {
                $template_id = $field->value;
                $field_id = $field->id;
            }
            if ($field->type == 'cgtscolor') {
                $template_id = $field->value;
                $color_id = $field->id;
            }
        }
        if (($template_id) && ($template_id != $tmpl)) {
            // need to update template switcher field value
            $fieldmodel = new FieldModel(array('ignore_request' => true));
            $fieldmodel->setFieldValue($field_id, $userid, $tmpl);
        }
        if ($color_id) {
            $fieldmodel = new FieldModel(array('ignore_request' => true));
            if ($color > 0) {
                $fieldmodel->setFieldValue($color_id, $userid, 'yes');
            } else {
                $fieldmodel->setFieldValue($color_id, $userid, 'no');
            }
        }

        return new JsonResponse('ok');
    }
}
