<?php
/*
 * @package     RadicalMart Cart Module
 * @subpackage  mod_radicalmart_cart
 * @version     1.1.0
 * @author      Delo Design - delo-design.ru
 * @copyright   Copyright (c) 2022 Delo Design. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link        https://delo-design.ru/
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\Path;

return new class () implements ServiceProviderInterface {
	public function register(Container $container)
	{
		$container->set(InstallerScriptInterface::class, new class ($container->get(AdministratorApplication::class)) implements InstallerScriptInterface {
			/**
			 * The application object
			 *
			 * @var  AdministratorApplication
			 *
			 * @since  1.1.0
			 */
			protected AdministratorApplication $app;

			/**
			 * The Database object.
			 *
			 * @var   DatabaseDriver
			 *
			 * @since  1.1.0
			 */
			protected DatabaseDriver $db;

			/**
			 * Minimum Joomla version required to install the extension.
			 *
			 * @var  string
			 *
			 * @since  1.1.0
			 */
			protected string $minimumJoomla = '4.2';

			/**
			 * Minimum PHP version required to install the extension.
			 *
			 * @var  string
			 *
			 * @since  1.1.0
			 */
			protected string $minimumPhp = '7.4';

			/**
			 * Update methods.
			 *
			 * @var  array
			 *
			 * @since  1.1.0
			 */
			protected array $updateMethods = [
				'update1_1_0'
			];

			/**
			 * Constructor.
			 *
			 * @param   AdministratorApplication  $app  The applications object.
			 *
			 * @since 1.1.0
			 */
			public function __construct(AdministratorApplication $app)
			{
				$this->app = $app;
				$this->db  = Factory::getContainer()->get('DatabaseDriver');
			}

			/**
			 * Function called after the extension is installed.
			 *
			 * @param   InstallerAdapter  $adapter  The adapter calling this method
			 *
			 * @return  boolean  True on success
			 *
			 * @since   1.1.0
			 */
			public function install(InstallerAdapter $adapter): bool
			{
				return true;
			}

			/**
			 * Function called after the extension is updated.
			 *
			 * @param   InstallerAdapter  $adapter  The adapter calling this method
			 *
			 * @return  boolean  True on success
			 *
			 * @since   1.1.0
			 */
			public function update(InstallerAdapter $adapter): bool
			{
				// Refresh media version
				(new Version())->refreshMediaVersion();

				return true;
			}

			/**
			 * Function called after the extension is uninstalled.
			 *
			 * @param   InstallerAdapter  $adapter  The adapter calling this method
			 *
			 * @return  boolean  True on success
			 *
			 * @since   1.1.0
			 */
			public function uninstall(InstallerAdapter $adapter): bool
			{
				return true;
			}

			/**
			 * Function called before extension installation/update/removal procedure commences.
			 *
			 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
			 * @param   InstallerAdapter  $adapter  The adapter calling this method
			 *
			 * @return  boolean  True on success
			 *
			 * @since   1.1.0
			 */
			public function preflight(string $type, InstallerAdapter $adapter): bool
			{
				// Check compatible
				if (!$this->checkCompatible())
				{
					return false;
				}

				if ($type === 'update')
				{
					// Check update server
					$this->changeUpdateServer();
				}

				return true;
			}

			/**
			 * Function called after extension installation/update/removal procedure commences.
			 *
			 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
			 * @param   InstallerAdapter  $adapter  The adapter calling this method
			 *
			 * @return  boolean  True on success
			 *
			 * @since   1.1.0
			 */
			public function postflight(string $type, InstallerAdapter $adapter): bool
			{
				// Run updates script
				if ($type === 'update')
				{
					foreach ($this->updateMethods as $method)
					{
						if (method_exists($this, $method))
						{
							$this->$method($adapter);
						}
					}
				}

				return true;
			}

			/**
			 * Method to check compatible.
			 *
			 * @throws  \Exception
			 *
			 * @return  bool True on success, False on failure.
			 *
			 * @since  1.1.0
			 */
			protected function checkCompatible(): bool
			{
				$app = Factory::getApplication();

				// Check joomla version
				if (!(new Version())->isCompatible($this->minimumJoomla))
				{
					$app->enqueueMessage(Text::sprintf('MOD_RADICALMART_CART_ERROR_COMPATIBLE_JOOMLA', $this->minimumJoomla),
						'error');

					return false;
				}

				// Check PHP
				if (!(version_compare(PHP_VERSION, $this->minimumPhp) >= 0))
				{
					$app->enqueueMessage(Text::sprintf('MOD_RADICALMART_CART_ERROR_COMPATIBLE_PHP', $this->minimumPhp),
						'error');

					return false;
				}

				return true;
			}

			/**
			 * Method to change current update server.
			 *
			 * @throws  \Exception
			 *
			 * @since  1.1.0
			 */
			protected function changeUpdateServer()
			{
				$old = 'https://radicalmart.ru/update?element=mod_radicalmart_cart';
				$new = 'https://sovmart.ru/update?element=mod_radicalmart_cart';

				$db    = $this->db;
				$query = $db->getQuery(true)
					->select(['update_site_id', 'location'])
					->from($db->quoteName('#__update_sites'))
					->where($db->quoteName('location') . ' = :location')
					->bind(':location', $old);
				if ($update = $db->setQuery($query)->loadObject())
				{
					$update->name     = 'RadicalMart Module: Cart';
					$update->location = $new;
					$db->updateObject('#__update_sites', $update, 'update_site_id');
				}
			}

			/**
			 * Method to update to 1.1.0 version.
			 *
			 * @since  1.1.0
			 */
			protected function update1_1_0()
			{
				// Remove old entry point
				$file = Path::clean(JPATH_ROOT . '/modules/mod_radicalmart_cart/mod_radicalmart_cart.php');
				if (File::exists($file))
				{
					File::delete($file);
				}
			}
		});
	}
};