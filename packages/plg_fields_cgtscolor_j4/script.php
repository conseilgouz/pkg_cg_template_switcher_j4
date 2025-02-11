<?php
/*
* CG template switcher colors Field plugin
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Log\Log;
use Joomla\Component\Fields\Administrator\Model\FieldModel;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class plgfieldscgtscolorInstallerScript
{
    private $min_joomla_version     = '4.0.0';
    private $min_php_version        = '8.0';
    private $name                   = 'Plugin CG Template Switcher Color';
    private $exttype                = 'plugin';
    private $extfolder              = 'fields';
    private $extname                = 'cgtscolor';
    private $extension              = 'plg_fields_cgtscolor';
    private $previous_version       = '';
    private $dir           = null;
    private $lang;
    private $installerName = 'plgsystemcgtscolorinstaller';
    public function __construct()
    {
        $this->dir = __DIR__;
        $this->lang = Factory::getApplication()->getLanguage();
    }
	function uninstall($parent) {
		$this->loadLanguage();
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__fields');
        $query->where('type = ' . $db->quote('cgtscolor'));
        $query->where('state = 1');
        $query->setLimit(1);
        $db->setQuery($query);
        $found = $db->loadObject();
        if (!$found) {// Found in db => exit
            return;
        }
        // delete field values
        $query   = $db->getQuery(true);
        $query->delete($db->quoteName('#__fields_values'))
              ->where($db->quoteName('field_id') . ' = :fieldid')
              ->bind(':fieldid', $found->id, \Joomla\Database\ParameterType::INTEGER);
        $db->setQuery($query);
        $db->execute();
        // update field state to -2
        $conditions = array(
            $db->qn('id') . ' = ' . (int)$found->id,
            $db->qn('state') . ' = 1'
        );
        $fields = array($db->qn('state') . ' = -2');

        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__fields'))->set($fields)->where($conditions);
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (RuntimeException $e) {
            Log::add('unable to enable '.$this->name, Log::ERROR, 'jerror');
        }
        
        
        // delete field
        $field = new FieldModel(array('ignore_request' => true));
        $key = [$found->id];
        $ret = $field->delete($key);
		return true;
	}
    public function preflight($type, $parent)
    {
        $this->loadLanguage();
        if (! $this->passMinimumJoomlaVersion()) {
            $this->uninstallInstaller();
            return false;
        }

        if (! $this->passMinimumPHPVersion()) {
            $this->uninstallInstaller();
            return false;
        }
        // To prevent installer from running twice if installing multiple extensions
        if (! file_exists($this->dir . '/' . $this->installerName . '.xml')) {
            return true;
        }
    }

    public function postflight($type, $parent)
    {
        $this->loadLanguage();
        if (($type == 'install') || ($type == 'update')) {
            $this->postinstall_cleanup();
        }

        return true;
    }
    private function postinstall_cleanup()
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        //---------------- remove obsolete update sites -------------
        $query = $db->getQuery(true)
            ->delete('#__update_sites')
            ->where($db->quoteName('location') . ' like "%432473037d.url-de-test.ws/%"');
        $db->setQuery($query);
        $db->execute();
        //---------------- enable plugin ----------------------------
        $conditions = array(
            $db->qn('type') . ' = ' . $db->q($this->exttype),
            $db->qn('folder') . ' = ' . $db->q($this->extfolder),
            $db->qn('element') . ' = ' . $db->quote($this->extname)
        );
        $fields = array($db->qn('enabled') . ' = 1');

        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (RuntimeException $e) {
            Log::add('unable to enable '.$this->name, Log::ERROR, 'jerror');
        }
        $this->check_field();
    }
    private function check_field()
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__fields');
        $query->where('type = ' . $db->quote('cgtscolor'));
        $query->where('state = 1');
        $query->setLimit(1);
        $db->setQuery($query);
        $found = $db->loadResult();
        if ($found) {// Found in db => exit
            return;
        }
        $field = new FieldModel(array('ignore_request' => true));
        $data = [];
        $data['id']     = 0;
        $data['type'] = 'cgtscolor';
        $data['title'] = Text::_('PLG_FIELDS_CGTSCOLOR_LABEL');
        $data['label'] = Text::_('PLG_FIELDS_CGTSCOLOR_LABEL');
        $data['params'] = [];
        $data['context'] = 'com_users.user';
        $data['description'] = '';
        $data['default_value'] = 'no';
        $data['state'] = true;
        $data['language'] = '*';
        // enable update on frontend
        $rules = [];
        $rules['core.edit.value'] = [1 => true];
        $data['rules'] = $rules;
        $field->save($data);

        Factory::getApplication()->enqueueMessage(Text::_('PLG_FIELDS_CGTSCOLOR_OK'), 'notice');
    }

    public function loadLanguage()
    {
        $path = JPATH_ADMINISTRATOR . '/language/';
        $this->lang->load($this->extension, $path, 'en-GB', true);
        $this->lang->load($this->extension, $path, $this->lang->getDefault(), true);
        $this->lang->load($this->extension, $path, null, true);
        $this->lang->load($this->extension . '.sys', $path, 'en-GB', true);
        $this->lang->load($this->extension . '.sys', $path, $this->lang->getDefault(), true);
        $this->lang->load($this->extension . '.sys', $path, null, true);
        return true;
    }


    // Check if Joomla version passes minimum requirement
    private function passMinimumJoomlaVersion()
    {
        $j = new Version();
        $version = $j->getShortVersion();
        if (version_compare($version, $this->min_joomla_version, '<')) {
            Factory::getApplication()->enqueueMessage(
                'Incompatible Joomla version : found <strong>' . $version . '</strong>, Minimum : <strong>' . $this->min_joomla_version . '</strong>',
                'error'
            );

            return false;
        }

        return true;
    }

    // Check if PHP version passes minimum requirement
    private function passMinimumPHPVersion()
    {
        if (version_compare(PHP_VERSION, $this->min_php_version, '<')) {
            Factory::getApplication()->enqueueMessage(
                'Incompatible PHP version : found  <strong>' . PHP_VERSION . '</strong>, Minimum <strong>' . $this->min_php_version . '</strong>',
                'error'
            );
            return false;
        }
        return true;
    }

    private function uninstallInstaller()
    {
        if (! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
            return;
        }
        $this->delete([
            JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
            JPATH_PLUGINS . '/system/' . $this->installerName,
        ]);
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->delete('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->execute();
        Factory::getApplication()->getCache()->clean('_system');
    }

    public function delete($files = [])
    {
        foreach ($files as $file) {
            if (is_dir($file)) {
                Folder::delete($file);
            }

            if (is_file($file)) {
                File::delete($file);
            }
        }
    }

}
