<?php
/**
 * Plugin Name: Story4Dev
 * Description: Synchronise les rapports d'un projet sur story4dev.com
 * Version: 1.0.0
 * Author: 
 * Text Domain: Story4Dev
 * Domain Path: /languages
 */
require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

if(!defined('S4D_DIR'))    define('S4D_DIR', plugin_dir_path(__FILE__));
if(!defined('S4D_FILE'))   define('S4D_FILE', __FILE__);
if(!defined('S4D_URL'))    define('S4D_URL', plugin_dir_url(__FILE__));

require_once('functions.php');

$actions = array(
    /** Translation & Script & Style */
    new \Story4Dev\WordPress\Translation\TextDomain(),
    new \Story4Dev\WordPress\Enqueue\Style(),
    new \Story4Dev\WordPress\Enqueue\Script(),
    
    /** Admin Menu */
    new \Story4Dev\WordPress\Admin\Menus(),
);

$wppd = new \Story4Dev\Story4Dev($actions);
$wppd->execute(S4D_FILE);