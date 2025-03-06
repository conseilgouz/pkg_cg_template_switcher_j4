<?php
/**
* CG Template Switcher package  - Joomla 4.x/5.x Module 
* Package			: CG Template Switcher
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;

class pkg_CGTemplateSwitcherInstallerScript
{
	private $min_joomla_version      = '3.10.0';
	private $min_php_version         = '7.4';
	private $extname                 = 'cg_template_switcher';
	private $dir           = null;
	public function __construct()
	{
		$this->dir = __DIR__;
	}
   /**
    *
    * Function to run when un-installing the component
    * @return void
    */
   public function uninstall($parent) {
		// remove old package xml
		$f = JPATH_MANIFESTS . '/packages/pkg_cg_template_switcher.xml';
		if (@is_file($f)) {
			File::delete($f);
		}
		$f = JPATH_MANIFESTS . '/packages/pkg_cgtemplateswitcher.xml';
		if (@is_file($f)) {
			File::delete($f);
		}
		// remove old package path
		$f = JPATH_MANIFESTS . '/packages/CG Template Switcher';
		if (!@file_exists($f) || !is_dir($f) || is_link($f)) {
			return;
		}
		Folder::delete($f);
   }

    function preflight($type, $parent)
    {
		if ( ! $this->passMinimumJoomlaVersion())
		{
			$this->uninstallInstaller();
			return false;
		}

		if ( ! $this->passMinimumPHPVersion())
		{
			$this->uninstallInstaller();
			return false;
		}
    }
    
    function postflight($type, $parent)
    {
		if (($type=='install') || ($type == 'update')) { // remove obsolete dir/files
			$this->postinstall_cleanup();
			$this->postinstall_enable_plugin();
		}

		return true;
    }
	private function postinstall_cleanup() {
		$obsloteFolders = ['css', 'js', 'language','lib','models'];
		// Remove plugins' files which load outside of the component. If any is not fully updated your site won't crash.
		foreach ($obsloteFolders as $folder)
		{
			$f = JPATH_SITE . '/modules/mod_'.$this->extname.'/' . $folder;

			if (!@file_exists($f) || !is_dir($f) || is_link($f))
			{
				continue;
			}

			Folder::delete($f);
		}
		// remove fancybox in media
		$f = JPATH_SITE . '/media/mod_'.$this->extname.'/fancybox';
		if (@file_exists($f) && is_dir($f)) {
			Folder::delete($f);
		}
		$obsloteFiles = [sprintf("%s/modules/mod_%s/helper.php", JPATH_SITE, $this->extname),
                         sprintf("%s/modules/mod_%s/lighbox.html", JPATH_SITE, $this->extname), // test file
                         sprintf("%s/modules/mod_%s/mod_cg_template_switcher.php", JPATH_SITE, $this->extname),
						 sprintf("%s/modules/mod_%s/script.php", JPATH_SITE, $this->extname),
						 sprintf("%s/modules/mod_%s/tmpl/lighbox.html", JPATH_SITE, $this->extname),
						];
		foreach ($obsloteFiles as $file)
		{
			if (@is_file($file))
			{
				File::delete($file);
			}
		}
		$j = new Version();
		$version=$j->getShortVersion(); 
		$version_arr = explode('.',$version);
		if (($version_arr[0] == "4") || (($version_arr[0] == "3") && ($version_arr[1] == "10"))) {
			// Delete 3.9 and older language files
			$pluginname = "system_cgstyle";
			$langFiles = [
				sprintf("%s/language/en-GB/en-GB.mod_%s.ini", JPATH_SITE, $this->extname),
				sprintf("%s/language/en-GB/en-GB.mod_%s.sys.ini", JPATH_SITE, $this->extname),
				sprintf("%s/language/fr-FR/fr-FR.mod_%s.ini", JPATH_SITE, $this->extname),
				sprintf("%s/language/fr-FR/fr-FR.mod_%s.sys.ini", JPATH_SITE, $this->extname),
				sprintf("%s/language/en-GB/en-GB.plg_%s.ini", JPATH_ADMINISTRATOR, $pluginname),
				sprintf("%s/language/en-GB/en-GB.plg_%s.sys.ini", JPATH_ADMINISTRATOR, $pluginname),
				sprintf("%s/language/fr-FR/fr-FR.plg_%s.ini", JPATH_ADMINISTRATOR, $pluginname),
				sprintf("%s/language/fr-FR/fr-FR.plg_%s.sys.ini", JPATH_ADMINISTRATOR, $pluginname),
			];
			foreach ($langFiles as $file) {
				if (@is_file($file)) {
					File::delete($file);
				}
			}
		}
		// Joomla 3.10 : uppercas manifest file name
		$f = JPATH_MANIFESTS . '/packages/pkg_cgtemplateswitcher.xml';
		$u = JPATH_MANIFESTS . '/packages/pkg_CGTemplateSwitcher.xml';
		try { 
			File::copy($f,$u);
		} catch (RuntimeException $e) {
		}
		// remove obsolete update sites
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->delete('#__update_sites')
			->where($db->quoteName('location') . ' like "%432473037d.url-de-test.ws/%"');
		$db->setQuery($query);
		$db->execute();
		// Simple Isotope is now on Github
		$query = $db->getQuery(true)
			->delete('#__update_sites')
			->where($db->quoteName('location') . ' like "%conseilgouz.com/updates/mod_cg_template%"');
		$db->setQuery($query);
		$db->execute();
		$query = $db->getQuery(true)
			->delete('#__update_sites')
			->where($db->quoteName('location') . ' like "%conseilgouz.com/updates/pkg_cg_template%"');
		$db->setQuery($query);
		$db->execute();
		
	}
	// enable CGStyle plugin
	private function postinstall_enable_plugin() {
		// enable plugin
		$db = Factory::getContainer()->get(DatabaseInterface::class);
        $conditions = array(
            $db->qn('type') . ' = ' . $db->q('plugin'),
            $db->qn('element') . ' = ' . $db->quote('cgstyle')
        );
        $fields = array($db->qn('enabled') . ' = 1');

        $query = $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($query);
        try {
	        $db->execute();
        }
        catch (RuntimeException $e) {
           Factory::getApplication()->enqueueMessage('unable to enable Plugin CGStyle',  'jerror');
        }
	}
	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		$j = new Version();
		$version=$j->getShortVersion(); 
		if (version_compare($version, $this->min_joomla_version, '<'))
		{
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

		if (version_compare(PHP_VERSION, $this->min_php_version, '<'))
		{
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
		if ( ! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
			return;
		}
		$this->delete([
			JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
			JPATH_PLUGINS . '/system/' . $this->installerName,
			sprintf("%s/modules/mod_%s/script.php", JPATH_SITE, $this->extname)
		]);
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		Factory::getCache()->clean('_system');
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