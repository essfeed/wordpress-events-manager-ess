<?php
/**
  * Model ESS_IO (Input/Output)
  * Manage the I/O between user, local server and web-services
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */
final class ESS_IO
{
	const EM_ESS_ARGUMENT 	= 'em_ess';

	const HTTP				= 'http://';
	const ESS_WEBSITE 		= 'http://essfeed.org';
	const PLUGIN_WEBSITE	= 'http://wp-events-plugin.com';
	const HYPECAL_WEBSITE	= 'http://www.hypecal.com';
	const CURL_LIB_URL		= 'http://php.net/manual/en/book.curl.php';

	const CRON_EVENT_HOOK	= 'ESS_daily_event_hook';

	function __construct() {}


	public static function set_filters_handler()
	{
		if ( has_filter( 'the_content', 'em_content' ) )
		{
			//d( has_filter( 'the_content', 'em_content' ) );

			if ( get_option( 'ess_feed_visibility_meta', TRUE ) )
			{
				add_action( 'wp_meta', 				array( 'ESS_Elements',	'get_feed_meta_link' 		) );
				add_action( 'wp_head',				array( 'ESS_Elements', 	'get_feed_meta_header' 		) );
			}

			add_filter( 'init', 					array( 'ESS_IO', 		'set_ess_feed_handler' 		), TRUE );
			add_filter( 'rewrite_rules_array', 		array( 'ESS_IO', 		'get_rewrite_rules_array' 	) );
			add_filter( 'query_vars', 				array( 'ESS_IO', 		'get_query_vars' 			) );

			add_filter( 'em_content', 				array( 'ESS_Elements', 	'get_listing_content' 		) );
			add_filter( 'em_event_output_single', 	array( 'ESS_Elements', 	'get_output_single' 		) );

			add_action( 'admin_menu', 				array( 'ESS_IO', 		'set_admin_submenu_page' 	) );

			add_filter( 'em_deactivate', 			array( 'EM_ESS', 		'set_deactivation' 			) );

			ESS_IO::set_save_filter( TRUE );
		}
	}

	public static function set_save_filter( $activate=TRUE )
	{
		if ( $activate == TRUE )
		{
			if ( get_option( 'ess_feed_push', TRUE ) )
				add_filter( 'em_event_save', array( 'ESS_IO', 'set_event_saved_filter' ) );
		}
		else
		{
			if ( has_filter( 'em_event_save' ) )
				remove_filter( 'em_event_save', array( 'ESS_IO', 'set_event_saved_filter' ) );
		}
	}

	// -- Set Schedule Hook (CRON tasks)
	public static function set_crons()
	{
		if ( !wp_next_scheduled( ESS_IO::CRON_EVENT_HOOK ) )
	        wp_schedule_event( time(), 'daily', ESS_IO::CRON_EVENT_HOOK );

	    // DEBUG
	    //if ( !wp_next_scheduled( 'ESS_hourly_hook' ) )
	    //    wp_schedule_event( time(), 'tenminutely', 'ESS_hourly_hook' );
    }

	public static function unset_crons()
	{
		wp_clear_scheduled_hook( CRON_EVENT_HOOK );

		// DEBUG
		//wp_clear_scheduled_hook( 'ESS_hourly_hook' );
	}

	public static function set_activation()
	{
		flush_rewrite_rules();

		if ( !current_user_can( 'activate_plugins' ) )
            return;

        $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : EM_ESS::NAME;
        check_admin_referer( "activate-plugin_{$plugin}" );

		if ( !EM_MS_GLOBAL || ( EM_MS_GLOBAL && is_main_blog() ) )
			ESS_Database::createTable();

		ESS_IO::set_crons();
	}

	public static function set_deactivation()
	{
		if ( !current_user_can( 'activate_plugins' ) )
        	return;

        $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : EM_ESS::NAME;
        check_admin_referer( "deactivate-plugin_{$plugin}" );

		// DEBUG: remove DB while desactivating the plugin
		//if( !EM_MS_GLOBAL || (EM_MS_GLOBAL && is_main_blog()) )
		//	ESS_Database::deteteTable();

		// -- Remove Schedule Hook (CRON tasks)
		ESS_IO::unset_crons();
	}

	public static function set_uninstall()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;

        check_admin_referer( 'bulk-plugins' );

		if( !EM_MS_GLOBAL || (EM_MS_GLOBAL && is_main_blog()) )
			ESS_Database::deteteTable();

		// -- Remove Schedule Hook (CRON tasks)
		ESS_IO::unset_crons();

		// Important: Check if the file is the one that was registered during the uninstall hook.
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            return;
    }




	public static function set_ess_feed_handler()
	{
		if ( preg_match( '/^\/?em_ess\/?$/', $_SERVER['REQUEST_URI']) || !empty( $_REQUEST[ ESS_IO::EM_ESS_ARGUMENT ] ) )
		{
			ESS_Feed::output(
				( ( isset( $_REQUEST[ 'event_id' ] ) )? $_REQUEST[ 'event_id' ] : '' ),
				( ( isset( $_REQUEST[ 'page' ] ) )? $_REQUEST[ 'page' ] : '' ),
				( isset( $_REQUEST[ 'download' ] )? ( ( intval( $_REQUEST[ 'download' ] ) >= 1 )? TRUE : FALSE ) : FALSE ),
				( isset( $_REQUEST[ 'push' ] 	 )? ( ( intval( $_REQUEST[ 'push' ] 	) >= 1 )? TRUE : FALSE ) : FALSE )
			);
			die;
		}
	}

	public static function set_admin_submenu_page()
	{
		$plugin_pages = array();

	   	$plugin_pages['help'] = add_submenu_page(
	   		'edit.php?post_type='.EM_POST_TYPE_EVENT,
	   		__('Import/Export events with ESS','dbem'),
	   		__('ESS Feed','dbem'),
	   		'list_users',
	   		"events-manager-admin-ess",
	   		array( 'ESS_Admin', 'main_page' )
		);

		$plugin_pages = apply_filters( 'em_create_events_submenu', $plugin_pages );
	}

	/**
     * Get a usable temp directory
     *
     * Adapted from Solar/Dir.php
     * @author Paul M. Jones <pmjones@solarphp.com>
     * @license http://opensource.org/licenses/bsd-license.php BSD
     * @link http://solarphp.com/trac/core/browser/trunk/Solar/Dir.php
     *
     * @return string
     */
    public static function tmp()
    {
        static $tmp = null;

        if ( !$tmp )
        {
            $tmp = function_exists( 'sys_get_temp_dir' )? sys_get_temp_dir() : ESS_IO::_tmp();
			$tmp = rtrim( $tmp, DIRECTORY_SEPARATOR );
        }
        return $tmp;
    }

    /**
     * Returns the OS-specific directory for temporary files
     *
     * @author Paul M. Jones <pmjones@solarphp.com>
     * @license http://opensource.org/licenses/bsd-license.php BSD
     * @link http://solarphp.com/trac/core/browser/trunk/Solar/Dir.php
     *
     * @return string
     */
    public static function _tmp()
    {
        // non-Windows system?
        if ( strtolower( substr( PHP_OS, 0, 3 ) ) != 'win' )
        {
            $tmp = empty($_ENV['TMPDIR']) ? getenv( 'TMPDIR' ) : $_ENV['TMPDIR'];
            return ($tmp)? $tmp : '/tmp';
        }

        // Windows 'TEMP'
        $tmp = empty($_ENV['TEMP']) ? getenv('TEMP') : $_ENV['TEMP'];
        if ($tmp) return $tmp;

        // Windows 'TMP'
        $tmp = empty($_ENV['TMP']) ? getenv('TMP') : $_ENV['TMP'];
        if ($tmp) return $tmp;

       	// Windows 'windir'
        $tmp = empty($_ENV['windir']) ? getenv('windir') : $_ENV['windir'];
        if ($tmp) return $tmp;

        // final fallback for Windows
        return getenv('SystemRoot') . '\\temp';
    }

	public static function get_feed_url( $event_id=NULL, $download=FALSE, $push=FALSE )
	{
		return trailingslashit( home_url() ) .
										   "?" . ESS_IO::EM_ESS_ARGUMENT . "=1".
			( ( intval( $event_id ) > 0 )? "&event_id=" . $event_id : '' ).
			( ( $push 	  == TRUE 		)? "&push=1"				: '' ).
			( ( $download == TRUE 		)? "&download=1"			: '' );
	}

	public static function get_rewrite_rules_array( $rules )
	{
		return $rules + array( "/ess/?$"=>'index.php?'. ESS_IO::EM_ESS_ARGUMENT . '=1' );
	}

	public static function get_query_vars( $vars )
	{
		array_push( $vars, ESS_IO::EM_ESS_ARGUMENT );
		return $vars;
	}

	public static function is_file_exists( $file_url=null, $maxTime=1000 )
	{
		if ( !is_null( $file_url ) )
		{
			$ch = @curl_init( $file_url );

			if ( $ch != FALSE )
			{
				curl_setopt( $ch, CURLOPT_NOBODY, 		TRUE );
				curl_setopt( $ch, CURLOPT_TIMEOUT_MS, 	$maxTime ); // execution timeout in miliseconds
				curl_exec( $ch );
				$retcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE ); // $retcode > 400 -> not found, $retcode = 200 -> found.
				curl_close( $ch );

				return ( $retcode == 200 )? TRUE : FALSE;
			}
			else
			{
				if ( ini_get( 'allow_url_fopen' ) )
					return ( file_get_contents( $file_url )? TRUE : FALSE );
			}
		}
		return FALSE;
	}

	public static function get_curl_result( $target_url, $feed_url='' )
	{
		$ch = @curl_init();

		$post_data = array(
			'REMOTE_ADDR' 	=> @$_SERVER[ 'REMOTE_ADDR' ],
			'SERVER_ADMIN'	=> @$_SERVER[ 'SERVER_ADMIN' ],
			'PROTOCOL'		=> ( ( stripos( @$_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === TRUE )? 'https://' : 'http://' ),
			'HTTP_HOST'		=> @$_SERVER[ 'HTTP_HOST' ],
			'REQUEST_URI'	=> @$_SERVER[ 'REQUEST_URI' ],
			'feed'			=> urlencode( $feed_url )
		);

		if ( $ch != FALSE )
		{
			curl_setopt( $ch, CURLOPT_URL, 				$target_url );
			curl_setopt( $ch, CURLOPT_COOKIEJAR,  		ESS_IO::tmp() . '/cookies' );
			curl_setopt( $ch, CURLOPT_REFERER, 			@$_SERVER[ 'REQUEST_URI' ] );
			curl_setopt( $ch, CURLOPT_POSTFIELDS,  		$post_data );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 	1 );
			curl_setopt( $ch, CURLOPT_VERBOSE, 			1 );
			curl_setopt( $ch, CURLOPT_FAILONERROR, 		1 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 			10 );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 	0 );

			$result = curl_exec( $ch );

			//d( $target_url, $feed_url, urlencode( $feed_url ), $result );

			return $result;
		}
		else
		{
			if ( ini_get( 'allow_url_fopen' ) )
			{
				$opts = array(
					'http' => array(
						'method'  => 'POST',
						'header'  => "Content-Type: application/x-www-form-urlencoded",
						'content' => http_build_query( $post_data ),
						'timeout' => (60*20)
					)
				);

				$result = @file_get_contents( $target_url, FALSE, stream_context_create( $opts ), -1, 40000 );

				//d( $result );

				return $result;
			}
			else
			{
				$file = $target_url . "?";

				foreach ( $post_data as $att => $value )
					$file .= $att . "=" . urlencode( $value ) . "&";

				$result = @exec( "wget -q \"" . $file . "\"" );

				if ( $result == FALSE )
				{
					global $ESS_Notices;

					$ESS_Notices->add_error(
						__( "PHP cURL must be installed on your server or PHP parameter 'allow_url_fopen' must be set to TRUE: ", 'dbem' ).
						ESS_Elements::get_curl_lib_link()
					);
				}
				else
					return $result;
			}
		}
		return FALSE;
	}

	public static function set_event_saved_filter( $result=NULL, $event_id=NULL )
	{
		if ( $result == TRUE )
		{
			if ( empty( $event_id ) ) global $EM_Event;
			else							 $EM_Event = em_get_event( $event_id );

			if ( $EM_Event instanceof EM_Event && intval( $EM_Event->event_id ) > 0 )
			{
				$feed_url = ESS_IO::get_feed_url( $EM_Event->event_id, FALSE, TRUE );

				return ( ESS_IO::get_curl_result( FeedWriter::$AGGREGATOR_WS, $feed_url ) !== FALSE );
			}
		}
		return FALSE;
	}
}
