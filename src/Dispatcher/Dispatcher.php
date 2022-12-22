<?php
/*
 * @package     RadicalMart Cart Module
 * @subpackage  mod_radicalmart_cart
 * @version     __DEPLOY_VERSION__
 * @author      Delo Design - delo-design.ru
 * @copyright   Copyright (c) 2022 Delo Design. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link        https://delo-design.ru/
 */

namespace Joomla\Module\RadicalMartCart\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Runs the dispatcher.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function dispatch()
	{
		parent::dispatch();

		Factory::getApplication()->getLanguage()->load('com_radicalmart');
	}
}