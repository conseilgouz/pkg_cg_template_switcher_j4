<?php
/**
 * @package CG template switcher Module
 * @subpackage  system.cg_style
 *
 * @copyright   Copyright (C) 2025 Conseilgouz. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

namespace ConseilGouz\Plugin\Fields\Cgtemplateswitcher\Field;

// Prevent direct access
defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\String\StringHelper;

class VersionField extends FormField
{
    /**
     * Element name
     *
     * @var   string
     */
    protected $_name = 'Version';
    protected $def;

    public function getInput()
    {
        $return = '';
        // Load language
        $extension = $this->def('extension');

        $version = '';

        $jinput = Factory::getApplication()->input;
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName('manifest_cache'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . '=' . $db->Quote($extension))
            ->where($db->quoteName('folder') . '=' . $db->Quote('fields'));
        $db->setQuery($query, 0, 1);
        $row = $db->loadAssoc();
        $tmp = json_decode($row['manifest_cache']);
        $version = $tmp->version;

        $document = Factory::getApplication()->getDocument();
        $css = '';
        $css .= ".version {display:block;text-align:right;color:brown;font-size:10px;}";
        $css .= ".readonly.plg-desc {font-weight:normal;}";
        $css .= "fieldset.radio label {width:auto;}";
        $document->addStyleDeclaration($css);
        $margintop = $this->def('margintop');
        if (StringHelper::strlen($margintop)) {
            $js = "document.addEventListener('DOMContentLoaded', function() {
			vers = document.querySelector('.version');
			parent = vers.parentElement.parentElement;
			parent.style.marginTop = '".$margintop."';
			})";
            $document->addScriptDeclaration($js);
        }
        $return .= '<span class="version">' . Text::_('JVERSION') . ' ' . $version . "</span>";

        return $return;

    }
    public function def($val, $default = '')
    {
        return (isset($this->element[$val]) && (string) $this->element[$val] != '') ? (string) $this->element[$val] : $default;
    }

}
