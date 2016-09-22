<?php
/*
 * Plugin Name: Events Manager ESS
 * Version: 	1.4
 * Plugin URI: 	http://essfeed.org
 * Description: Integrates ESS Feed into Events Manager to import and export events.
 * Text Domain: em-ess
 * Domain Path: /languages
 * Author: 		ESSFeed, Brice Pissard, Robby
 * Author URI: 	http://www.robby.ai/add-events/ess/
 */
/*
 Copyright (c) 2014, Marcus Sykes (Events Manager), Brice Pissard (ESS)

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

if ( !defined( 'EM_ESS_NAME' 		) ) {define( 'EM_ESS_NAME', 	   trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );}
if ( !defined( 'EM_ESS_DIR'  		) ) {define( 'EM_ESS_DIR', 		   WP_PLUGIN_DIR . '/' . EM_ESS_NAME );}
if ( !defined( 'EM_ESS_URL'  		) ) {define( 'EM_ESS_URL', 		   WP_PLUGIN_URL . '/' . EM_ESS_NAME );}
if ( !defined( 'EM_ESS_PATH'        ) ) {define( 'EM_ESS_PATH',        plugin_dir_url( __FILE__ ));}
if ( !defined( 'EM_ESS_SECURE'      ) ) {define( 'EM_ESS_SECURE',      ((!empty($_SERVER['HTTPS']) && @$_SERVER['HTTPS'] !== 'off') || @$_SERVER['SERVER_PORT'] == 443 || stripos( @$_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === TRUE) ? TRUE : FALSE);}
if ( !defined( 'EM_ESS_DEBUG'       ) ) {define( 'EM_ESS_DEBUG',       FALSE );}
if ( !defined( 'EM_ESS_VERSION'		) ) {define( 'EM_ESS_VERSION', 	   '1.4' );}
if ( !defined( 'EM_ESS_VERSION_KEY'	) ) {define( 'EM_ESS_VERSION_KEY', 'ess_version' );}

add_option( EM_ESS_VERSION_KEY, EM_ESS_VERSION );
add_action( 'plugins_loaded', array( 'EM_ESS', 'init' ) );

require_once( EM_ESS_DIR . "/inc/models/ESS_Database.php" );
require_once( EM_ESS_DIR . "/inc/models/ESS_IO.php" );

if ( EM_ESS_DEBUG && class_exists( 'Kint' ) == FALSE )
{
    require_once( EM_ESS_DIR . "/inc/libs/kint/Kint.class.php" );
}

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
    	$dir = plugin_dir_path( __FILE__ );

    	// -- MODELS
		include_once( $dir . 'inc/models/ESS_Database.php' 	);
		include_once( $dir . 'inc/models/ESS_Images.php' 	);
		include_once( $dir . 'inc/models/ESS_IO.php' 		);
		include_once( $dir . 'inc/models/ESS_Notices.php' 	);
		include_once( $dir . 'inc/models/ESS_Sounds.php' 	);
		include_once( $dir . 'inc/models/ESS_Timezone.php' 	);
		include_once( $dir . 'inc/models/ESS_Videos.php' 	);

    	// -- VIEWS
		include_once( $dir . 'inc/views/ESS_Admin.php' 		);
		include_once( $dir . 'inc/views/ESS_Elements.php' 	);
		include_once( $dir . 'inc/views/ESS_Feed.php' 		);

    	// -- CONTROLLERS
		include_once( $dir . 'inc/controllers/ESS_Control_admin.php' );
		include_once( $dir . 'inc/controllers/ESS_Import.php'	 	 );

		if ( class_exists( 'FeedWriter' ) == FALSE ) {
			require_once( EM_ESS_DIR . "/inc/libs/ess/FeedWriter.php" );
        }

		ESS_Notices::set_notices_global_handler();
		ESS_IO::set_filters_handler();
		ESS_Database::set_default_values();
    }

	public static function init()
	{
		if( !defined( 'EM_VERSION' ) ) {return;} // EM is not installed.

		is_null( self::$instance ) AND self::$instance = new self;
        return self::$instance;
	}
}
register_activation_hook( 	__FILE__, 	array( 'ESS_IO', 		'set_activation' 		) );
register_deactivation_hook( __FILE__, 	array( 'ESS_IO', 		'set_deactivation' 		) );
register_uninstall_hook(    __FILE__, 	array( 'ESS_IO', 		'set_uninstall' 		) );

add_action( ESS_IO::CRON_EVENT_HOOK, "update_feeds_daily" );

function update_feeds_daily()
{
    EM_ESS::load_MVC_files();

    if ( class_exists('ESS_Database' ) )
    {
        ESS_Database::update_feeds_daily();
    }
}
