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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use YOOtheme\Theme\Joomla\ThemeLoader;

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
        $cookieValue = Factory::getApplication()->input->cookie->get('cg_template');

        if ($cookieValue) {
            $db = $this->getDatabase();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__template_styles');
            $query->where('client_id = 0');
            $query->where('id = ' . (int)$cookieValue);
            $db->setQuery($query);
            $style = $db->loadObject();
            if ($style != null) {
                $j = new Version();
                $version = substr($j->getShortVersion(), 0, 1);
                if ($version >= "4") { // Joomla 4 and higher

                    $app->setTemplate($style);

                    if (strpos($style->template, 'yootheme') === 0) {
                        app()->call([ThemeLoader::class, 'initTheme']);
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
