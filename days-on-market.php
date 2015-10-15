<?php namespace ColdTurkey\DaysOnMarket;
/*
 * Plugin Name: Days On Market
 * Version: 1.4.2
 * Plugin URI: http://www.coldturkeygroup.com/
 * Description: A form for prospective home sellers to fill out to figure out how long it might take them to sell their home.
 * Author: Cold Turkey Group
 * Author URI: http://www.coldturkeygroup.com/
 * Requires at least: 4.0
 * Tested up to: 4.3
 *
 * @package Days On Market
 * @author Aaron Huisinga
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'DAYS_MARKET_PLUGIN_PATH' ) )
	define( 'DAYS_MARKET_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( ! defined( 'DAYS_MARKET_PLUGIN_VERSION' ) )
	define( 'DAYS_MARKET_PLUGIN_VERSION', '1.4.2' );

require_once( 'classes/class-days-on-market.php' );

global $days_market;
$days_market = new DaysOnMarket( __FILE__, new FrontDesk() );

if ( is_admin() ) {
	require_once( 'classes/class-days-on-market-admin.php' );
	new DaysOnMarket_Admin( __FILE__ );
}
