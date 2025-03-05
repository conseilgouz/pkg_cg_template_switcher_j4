<?php
/*
* CG template switcher Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Form\Field;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;
use ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Helper\CGTemplateSwitcherHelper;

class CgtemplateswitcherField extends FormField
{
    protected $type = 'cgtemplateswitcher';

    public function getInput()
    {
        $base	= 'media/plg_fields_cgtemplateswitcher/';
        $def_form = '';
        $templates =  CGTemplateSwitcherHelper::getTemplates($this);

        $app = Factory::getApplication();

        $list = $this->getSiteModules();
        $duration = 0;
        foreach ($list as $module) {
            $module_params = $module->params;
            $tmp_params = json_decode($module_params);
            if (isset($tmp_params->cookie_duration)) {
                $duration = $tmp_params->cookie_duration;
            }
        }
        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $document = $app->getDocument();
        $wa = $document->getWebAssetManager();
        $wa->registerAndUseScript('plgcgtemplateswitcher', $base.'js/init.js');
        $def = $this->value;
        if (!$this->value) {
            $def = 0;
            foreach ($templates->home as $ix => $template) {
                if ($template) {
                    $def = $ix;
                }
            }
        }
        $document->addScriptOptions(
            'plg_fields_cgtemplateswitcher',
            array('cookie_duration' => $duration )
        );

        $def_form .= HTMLHelper::_('select.genericlist', $templates->options, $this->name, "class=\"inputbox\" style=\"margin:0\"", 'value', 'text', $def, $this->id);

        return $def_form;
    }
    private function getSiteModules()
    {
        $module = 'mod_cg_template_switcher';
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery();
        $query->select('*');
        $query->from('#__modules');
        $query->where('module = :module');
        $query->where('published = 1');
        $query->bind(':module', $module, \Joomla\Database\ParameterType::STRING);
        $db->setQuery($query);
        $found = $db->loadObjectList();

        return $found;
    }
    
}
