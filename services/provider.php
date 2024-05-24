<?php
/*
 * @package     RadicalMart Cart Module
 * @subpackage  mod_radicalmart_cart
 * @version     __DEPLOY_VERSION__
 * @author      RadicalMart Team - radicalmart.ru
 * @copyright   Copyright (c) 2024 RadicalMart. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link         https://radicalmart.ru/
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	public function register(Container $container)
	{
		// Register services
		$container->registerServiceProvider(new ModuleDispatcherFactory('\\Joomla\\Module\\RadicalMartCart'));
		$container->registerServiceProvider(new HelperFactory('\\Joomla\\Module\\RadicalMartCart\\Site\\Helper'));
		$container->registerServiceProvider(new Module());
	}
};