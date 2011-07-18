<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @subpackage CampaignMonitor
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

Autoloader::add_core_namespace('CampaignMonitor');

Autoloader::add_classes(array(
	'CampaignMonitor\\CampaignMonitor'	=> __DIR__.'/classes/campaignmonitor.php',
));


/* End of file bootstrap.php */