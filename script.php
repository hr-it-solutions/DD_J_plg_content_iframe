<?php
/**
 * @package    DD_Iframe
 *
 * @author     HR-IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2019 - 2019 HR-IT-Solutions GmbH
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die();

/**
 * Class PlgContentDD_IframeInstallerScript
 *
 * @since  Version  1.0.0.0
 */

class PlgContentDD_IframeInstallerScript
{
	protected $name = 'DD Iframe';

	protected $extensionsToEnable = array(

		array(  'name'  => 'dd_iframe',
				'type'  => 'plugin',
				'group' => 'content')

	);

	/**
	 * Enable extensions
	 *
	 * @since Version  1.0.0.0
	 *
	 * @return void
	 */
	private function enableExtensions()
	{
		foreach ($this->extensionsToEnable as $extension)
		{
			$db  = JFactory::getDbo();
			$query = $db->getQuery(true)
					->update('#__extensions')
					->set($db->qn('enabled') . ' = ' . $db->q(1))
					->where('type = ' . $db->q($extension['type']))
					->where('element = ' . $db->q($extension['name']));

			if ($extension['type'] === 'plugin')
			{
				$query->where('folder = ' . $db->q($extension['group']));
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * JInstaller
	 *
	 * @param   object  $parent  \JInstallerAdapterPackageParent
	 *
	 * @return  boolean
	 *
	 * @since Version  1.0.0.0
	 */
	public function install($parent)
	{
		$this->enableExtensions();

		return true;
	}
}
