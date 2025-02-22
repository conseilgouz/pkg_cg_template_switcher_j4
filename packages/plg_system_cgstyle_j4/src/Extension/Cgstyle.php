<?php
/**
 * @package CG template switcher Module
 * @subpackage  system.cg_style
 *
 * @copyright   Copyright (C) 2025 Conseilgouz. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

namespace Conseilgouz\Plugin\System\Cgstyle\Extension;

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Version;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Component\Fields\Administrator\Model\FieldModel;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use YOOtheme\Theme\Joomla\ThemeLoader;
use YOOtheme\Config as YooConfig;
use YOOtheme\Event as YooEvent;

use function YOOtheme\app;

final class Cgstyle extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterRoute'   => 'afterRoute',
            'onBeforeRender' => 'onBeforeRender',
            'onAfterRender'  => 'onAfterRender'
        ];
    }
    public function afterRoute()
    {
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) { // run only on frontend
            return;
        }
        $user = Factory::getApplication()->getIdentity();
        $template_id = 0;

        $cookieValue = $app->input->cookie->getRaw('cg_template', ':');
        $cookie = explode(':', $cookieValue);

        if (sizeof($cookie) > 0) {
            $template_id = $cookie[0];
        }
        if ($template_id) {
            $db = $this->getDatabase();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__template_styles');
            $query->where('client_id = 0');
            $query->where('id = ' . (int)$template_id);
            $db->setQuery($query);
            $style = $db->loadObject();
            if ($style != null) {
                $lang = $app->getLanguage();
                if (!is_numeric($style->home)) { // home for a specific language
                    if ($style->home != $lang->getLanguage()) {
                        $this->loadLanguage();
                        $app->enqueueMessage(Text::_('CG_WRONG_LANGUAGE'), 'error');
                        $options = ['expires' => 'Thu, 01 Jan 1970 00:00:00 UTC',
                                    'path' => '/'];
                        $app->input->cookie->set('cg_template', "", $options);
                        if ($user->id) { // clean custom field
                            $this->clean_custom_fields($user);
                        }
                        return;
                    }
                }
                $j = new Version();
                $version = substr($j->getShortVersion(), 0, 1);
                if ($version >= "4") { // Joomla 4 and higher
                    $app->setTemplate($style);
                    if (strpos($style->template, 'yootheme') === 0) {
                        $config = app(YooConfig::class);
                        app()->call([ThemeLoader::class, 'initTheme']);
                        $config->set('theme.id', $style->id);
                        YooEvent::emit('theme.head');
                    }
                    if (strpos($style->template, 'astroid') === 0) {
                        \Astroid\Framework::getTemplate($style->id);
                    }
                } else { //  Joomla 3.10
                    $app->setTemplate($style->template, $style->params);
                }
            }
        }
    }
    private function clean_custom_fields($user)
    {
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
        $fieldmodel = new FieldModel(array('ignore_request' => true));
        if ($field_id) {
            $fieldmodel->setFieldValue($field_id, $user->id, 0);
        }
        if ($color_id) {
            $fieldmodel->setFieldValue($color_id, $user->id, 'no');
        }
    }
    public function onBeforeRender(\Joomla\Event\Event $event): void
    {
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) { // run only on frontend
            return;
        }
        $cookieValue = $app->input->cookie->getRaw('cg_template', ':');
        $cookie = explode(':', $cookieValue);
        $default = 80; // init default grayscale value
        $gray = 0;
        $invert = 100;
        $list = $this->getSiteModules();
        // get default grayscale from first CG Template Switcher module
        foreach ($list as $module) {
            $module_params = $module->params;
            $tmp_params = json_decode($module_params);
            if (isset($tmp_params->grayscale) && $tmp_params->grayscale) {
                $default = $tmp_params->grayscale;
            }
            if (isset($tmp_params->invert) && $tmp_params->invert) {
                $invert = $tmp_params->invert;
            }
        }
        $graycss = "";
        if (isset($cookie[1])) {
            if ((int)$cookie[1]) {// grayscale in cookie ?
                $gray = (int)$cookie[1];
            }
        }
        if (!$gray) { // empty or null : take default value
            $gray = $default;
        }
        $graycss = ".cgcolor {filter: grayscale($gray%) invert($invert%)}
.cgcolor img { filter: brightness(1.1) contrast(1.2) invert($invert%) grayscale(0) }";
        $customcss = $this->params->get('customcss', '');

        $customCSS = <<< CSS
$graycss
$customcss
CSS;

        $wa = $app->getDocument()->getWebAssetManager();
        $wa->addInlineStyle($customCSS, ['name' => 'cgcust.asset']);

    }
    // implement color change : add style filter in html
    public function onAfterRender(\Joomla\Event\Event $event): void
    {
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) { // run only on frontend
            return;
        }
        $cookieValue = $app->input->cookie->getRaw('cg_template', ':');
        $cookie = explode(':', $cookieValue);
        if ((sizeof($cookie) < 2) || ($cookie[1] == 0)) { // no color change
            return;
        }
        if ($cookie[1] == "bootstrap") {
            $this->addBSHeader();
            return;
        }
        if (($cookie[1] == 'tmpl') || !(int)$cookie[1]) {
            return;
        }
        // Make sure we have the `<html` opening tag
        $body = $app->getBody();
        if (stripos($body, '<body') === false) {
            return;
        }
        $class = " cgcolor ";

        $body = preg_replace_callback(
            '#<body(.*?)class\s*=\s*"(.*?)"(.*?)>#',
            function ($matches) use ($class) {
                return sprintf(
                    '<body%sclass="%s %s"%s>',
                    $matches[1],
                    $class,
                    $matches[2],
                    $matches[3]
                );
            },
            $body
        );

        $app->setBody($body);

    }
    private function getSiteModules()
    {
        $module = 'mod_cg_template_switcher';
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__modules');
        $query->where('module = :module');
        $query->where('published = 1');
        $query->bind(':module', $module, \Joomla\Database\ParameterType::STRING);
        $db->setQuery($query);
        $found = $db->loadObjectList();

        return $found;
    }
    private function addBSHeader()
    {
        $body = $this->getApplication()->getBody();
        if (stripos($body, '<html') === false) {
            return;
        }
        $bs = 'data-bs-theme="dark"';

        $body = preg_replace_callback(
            '#<html(.*?)>#',
            function ($matches) use ($bs) {
                return sprintf(
                    '<html %s %s>',
                    $bs,
                    $matches[1],
                );
            },
            $body
        );

        $this->getApplication()->setBody($body);
    }
}
