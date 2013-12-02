<?php
/*
Plugin Name: 	Events Manager ESS
Version: 		0.95
Plugin URI: 	http://essfeed.org
Description: 	Integrates ESS Feed into Events Manager to import and export events.
Author: 		Marcus Sykes, Brice Prissard
Author URI: 	http://wp-events-plugin.com
*/
/*
 Copyright (c) 2013, Marcus Sykes (Events Manager), Brice Pissard (ESS)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( !defined( 'EM_ESS_NAME' 		) ) define( 'EM_ESS_NAME', 		 trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
if ( !defined( 'EM_ESS_DIR'  		) ) define( 'EM_ESS_DIR', 		 WP_PLUGIN_DIR . '/' . EM_ESS_NAME );
if ( !defined( 'EM_ESS_URL'  		) ) define( 'EM_ESS_URL', 		 WP_PLUGIN_URL . '/' . EM_ESS_NAME );
if ( !defined( 'EM_ESS_VERSION'		) ) define( 'EM_ESS_VERSION', 	 '0.5' );
if ( !defined( 'EM_ESS_VERSION_KEY'	) ) define( 'EM_ESS_VERSION_KEY','ess_version' );

add_option( EM_ESS_VERSION_KEY, EM_ESS_VERSION );
add_action( 'plugins_loaded', array( 'EM_ESS', 'init' ) );

require_once( EM_ESS_DIR . "/inc/models/ESS_Database.php" );
require_once( EM_ESS_DIR . "/inc/models/ESS_IO.php" );

if ( WP_DEBUG )
	require_once( EM_ESS_DIR . "/inc/libs/kint-0.9/Kint.class.php" );

final class EM_ESS
{
	protected static $instance;

	const NAME = 'em-ess';

	function __construct()
    {
        add_action( current_filter(), array( &$this, 'load_MVC_files' ), 30 );
    }

	public static function load_MVC_files()
    {
        foreach( glob( plugin_dir_path( __FILE__ ) .'inc/models/*.php' 		) as $file ) include_once $file;
		foreach( glob( plugin_dir_path( __FILE__ ) .'inc/views/*.php' 		) as $file ) include_once $file;
		foreach( glob( plugin_dir_path( __FILE__ ) .'inc/controllers/*.php' ) as $file ) include_once $file;

		require_once( EM_ESS_DIR . "/inc/libs/ess/FeedWriter.php" );

		ESS_Notices::set_notices_global_handler();
		ESS_IO::set_filters_handler();
		ESS_Database::set_default_values();
    }

	public static function init()
	{
		if( !defined( 'EM_VERSION' ) ) return; // EM is not istalled

		is_null( self::$instance ) AND self::$instance = new self;
        return self::$instance;
	}

}

register_activation_hook( 	__FILE__, 	array( 'ESS_IO', 'set_activation' 	) );
register_deactivation_hook( __FILE__, 	array( 'ESS_IO', 'set_deactivation' ) );
register_uninstall_hook(    __FILE__, 	array( 'ESS_IO', 'set_uninstall' 	) );