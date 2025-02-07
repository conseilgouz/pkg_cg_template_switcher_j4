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
use YOOtheme\Event;
use YOOtheme\Config;

use function YOOtheme\app;

final class Cgstyle extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterRoute'   => 'afterRoute'
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
        $field_id = 0;
        if ($user->id) {
            $test = FieldsHelper::getFields('com_users.user', $user);
            foreach ($test as $field) {
                if ($field->type == 'cgtemplateswitcher') {
                    $template_id = $field->value;
                    $field_id = $field->id;
                }
            }
        }
        $cookieValue = $app->input->cookie->get('cg_template');

        if ($field_id && $cookieValue && ($template_id != $cookieValue)) {
            // need to update template switcher field value
            $fieldmodel = new FieldModel(array('ignore_request' => true));
            $fieldmodel->setFieldValue($field_id, $user->id, $cookieValue);
        }

        if ($cookieValue) {
            $template_id = $cookieValue;
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
                            $fieldmodel = new FieldModel(array('ignore_request' => true));
                            $fieldmodel->setFieldValue($field_id, $user->id, 0);
                        }
                        return;
                    }
                }
                $j = new Version();
                $version = substr($j->getShortVersion(), 0, 1);
                if ($version >= "4") { // Joomla 4 and higher
                    $app->setTemplate($style);
                    if (strpos($style->template, 'yootheme') === 0) {
                        $config = app(Config::class);
                        app()->call([ThemeLoader::class, 'initTheme']);
                        $config->set('theme.id', $style->id);
                        Event::emit('theme.head');
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

}
