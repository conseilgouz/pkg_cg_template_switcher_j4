<?php
/**
 * @version		1.0.0
 * @package		CGWebp system plugin
 * @author		ConseilGouz
 * @copyright	Copyright (C) 2024 ConseilGouz. All rights reserved.
 * license      https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * From DJ-WEBP version 1.0.0
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Http\HttpFactory;
use Conseilgouz\Plugin\System\Cgstyle\Extension\Cgstyle;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.2.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
				$displatcher = $container->get(DispatcherInterface::class);
                $plugin = new Cgstyle(
                    $displatcher,
                    (array) PluginHelper::getPlugin('system', 'cgstyle')
                );
                $plugin->setDatabase($container->get(DatabaseInterface::class));
                $plugin->setApplication(Factory::getApplication());
                return $plugin;
            }
        );
    }
};
