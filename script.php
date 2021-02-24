<?php

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class plgFieldsSubformsInstallerScript
{
	public function install($parent)
	{
		jimport('joomla.filesystem.file');
        Factory::getDBO()->setQuery("UPDATE `#__extensions` SET `enabled` = 1 WHERE `type` = 'plugin' AND`element` = 'subforms'")->execute();
	}
}