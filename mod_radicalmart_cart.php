<?php
/*
 * @package     RadicalMart Package
 * @subpackage  mod_radicalmart_cart
 * @version     1.0.0
 * @author      Delo Design - delo-design.ru
 * @copyright   Copyright (c) 2021 Delo Design. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link        https://delo-design.ru/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

$componentParams = ComponentHelper::getParams('com_radicalmart');
Factory::getLanguage()->load('com_radicalmart');

if ($componentParams->get('mode') !== 'shop') return false;

require ModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));