<?php
/*
* CG template switcher color Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Plugin\Fields\Cgtscolor\Form\Field;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class CgtscolorField extends FormField
{
    protected $type = 'cgtscolor';

    public function getInput()
    {
        $base	= 'media/plg_fields_cgtscolor/';
        $app = Factory::getApplication();
        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $document = $app->getDocument();
        $wa = $document->getWebAssetManager();
        $wa->registerAndUseScript('plgcgtscolor', $base.'js/init.js');

        $list = $this->getSiteModules();
        $module_params = null;
        $duration = 0;
        $gray = 0;
        $oneclick = 'false';
        foreach ($list as $module) {
            $module_params = $module->params;
            $tmp_params = json_decode($module_params);
            if (!$duration && isset($tmp_params->cookie_duration)) {
                $duration = $tmp_params->cookie_duration;
            }
            if (isset($tmp_params->oneclick) && ($tmp_params->oneclick != 'false')) {
                $oneclick = $tmp_params->oneclick;
            }
            if (!$gray && isset($tmp_params->grayscale)) {
                $gray = $tmp_params->grayscale;
            }
        }
        $document->addScriptOptions(
            'plg_fields_cgtscolor',
            array('cookie_duration' => (int)$duration,'oneclick' => $oneclick,'gray' => (int)$gray )
        );

        $def = '';
        $def .= '<div class="switcher has-success cgtscolor">';
        $checked = "";
        if ($this->value == 'no') {
            $checked = 'checked="checked" class="active "';
        } else {
            $checked = 'class="valid form-control-success" aria-invalid="false;"';
        }
        $def .= '<input type="radio" id="'.$this->id.'0" name="'.$this->name.'" value="no" '.$checked.'>';
        $def .= '<label for="'.$this->id.'0" >'.Text::_("JNO").'</label>';
        $checked = "";
        if ($this->value == 'yes') {
            $checked = 'checked="checked" class="active "';
        } else {
            $checked = 'class="valid form-control-success" aria-invalid="false;"';
        }
        $def .= '<input type="radio" id="'.$this->id.'1" name="'.$this->name.'" value="yes" '.$checked.'>';
        $def .= '<label for="'.$this->id.'1" >'.Text::_("JYES").'</label>';
        $def .= '<span class="toggle-outside"><span class="toggle-inside"></span></span>';
        $def .= '</div>';

        return $def;
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
