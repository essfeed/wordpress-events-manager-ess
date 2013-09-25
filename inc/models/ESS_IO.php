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
	const HYPECAL_WEBSITE	= 'http://hypecal.com';
	const CURL_LIB_URL		= 'http://php.net/manual/en/book.curl.php';
	
	function __construct() {}
	
	
	public static function set_filters_handler()
	{
		if ( has_filter( 'the_content', 'em_content' ) )
		{
			if ( get_option( 'ess_feed_visibility_meta', true ) )
			{
				add_action( 'wp_meta', 				array( 'ESS_Elements',	'get_feed_meta_link' 		) );
				add_action( 'wp_head',				array( 'ESS_Elements', 	'get_feed_meta_header' 		) );
			}
			
			add_filter( 'init', 					array( 'ESS_IO', 		'set_feed_handler' 			) );
			add_filter( 'rewrite_rules_array', 		array( 'ESS_IO', 		'get_rewrite_rules_array' 	) );
			add_filter( 'query_vars', 				array( 'ESS_IO', 		'get_query_vars' 			) );
			
			add_filter( 'em_content', 				array( 'ESS_Elements', 	'get_listing_content' 		) );
			add_filter( 'em_event_output_single', 	array( 'ESS_Elements', 	'get_output_single' 		) );
			
			add_action( 'admin_menu', 				array( 'ESS_IO', 		'set_admin_submenu_page' 	) );
			add_action( 'daily_event_hook', 		array( 'ESS_Database', 	'update_feeds_daily' 		) );
			
			self::set_save_filter( true );
		}
	}

	public static function set_save_filter( $is_active=true )
	{
		if ( $is_active == true )
		{
			if ( get_option( 'ess_feed_push', true ) )
				add_filter( 'em_event_save', array( 'ESS_IO', 'set_event_saved_filter' ) );
		}
		else 
		{
			if ( has_filter( 'em_event_save' ) )
				remove_filter( 'em_event_save', array( 'ESS_IO', 'set_event_saved_filter' ) );
		}
	}
	
	public static function set_activation_schedule() 
	{
		wp_schedule_event( current_time( 'timestamp' ), 'daily', 'daily_event_hook' );
	}

	public static function set_deactivation_schedule() 
	{
		wp_clear_scheduled_hook( 'daily_event_hook' );
	}
	
	public static function set_feed_handler()
	{
		if ( preg_match( '/^\/?ess\/?$/', $_SERVER['REQUEST_URI']) || !empty( $_REQUEST[ self::EM_ESS_ARGUMENT ] ) )
		{
			ESS_Feed::output( 
				$_REQUEST[ 'event_id' ], 
				$_REQUEST[ 'page' ],
				( isset( $_REQUEST[ 'download' ] )? ( ( intval( $_REQUEST[ 'download' ] ) >= 1 )? true : false ) : false ),
				( isset( $_REQUEST[ 'push' ] 	 )? ( ( intval( $_REQUEST[ 'push' ] 	) >= 1 )? true : false ) : false )
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
	
	public static function get_tmp_path()
	{
		return ( ( strlen( sys_get_temp_dir() ) >= 0 )? sys_get_temp_dir() : "/tmp" );
	}
	
	public static function get_feed_url( $event_id=NULL, $download=false, $push=false )
	{
		return trailingslashit( home_url() ) . 
										   "?".self::EM_ESS_ARGUMENT."=1". 
			( ( intval( $event_id ) > 0 )? "&event_id=" . $event_id : '' ).
			( ( $push 	  == true 		)? "&push=1"				: '' ).
			( ( $download == true 		)? "&download=1"			: '' );
	}
	
	public static function get_rewrite_rules_array( $rules )
	{
		return $rules + array( "/ess/?$"=>'index.php?'. self::EM_ESS_ARGUMENT . '=1' );
	}
	
	public static function get_query_vars( $vars )
	{
		array_push( $vars, self::EM_ESS_ARGUMENT );
		return $vars;
	}
	
	public static function is_file_exists( $file_url=null, $maxTime=1000 )
	{
		if ( !is_null( $file_url ) ) 
		{
			$ch = @curl_init( $file_url );
			
			if ( $ch )
			{
				curl_setopt( $ch, CURLOPT_NOBODY, 		true );
				curl_setopt( $ch, CURLOPT_TIMEOUT_MS, 	$maxTime ); // execution timeout in miliseconds
				curl_exec( $ch );
				$retcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE ); // $retcode > 400 -> not found, $retcode = 200 -> found.
				curl_close( $ch );
				
				return ( $retcode == 200 )? true : false;
			}
		}
		return false;
	}
	
	public static function get_curl_result( $target_url, $feed_url='' )
	{
		$ch = @curl_init();
			
		if ( $ch !== false )
		{
			$post_data = array( 
				'REMOTE_ADDR' 	=> @$_SERVER[ 'REMOTE_ADDR' ],
				'SERVER_ADMIN'	=> @$_SERVER[ 'SERVER_ADMIN' ],
				'PROTOCOL'		=> ( ( stripos( @$_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === true )? 'https://' : 'http://' ),
				'HTTP_HOST'		=> @$_SERVER[ 'HTTP_HOST' ],
				'REQUEST_URI'	=> @$_SERVER[ 'REQUEST_URI' ],
				'feed'			=> $feed_url 
			);
			curl_setopt( $ch, CURLOPT_URL, 				$target_url );
			curl_setopt( $ch, CURLOPT_COOKIEJAR,  		self::get_tmp_path() . '/cookies' );
			curl_setopt( $ch, CURLOPT_REFERER, 			@$_SERVER[ 'REQUEST_URI' ] );
			curl_setopt( $ch, CURLOPT_POSTFIELDS,  		$post_data );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 	1 );
			curl_setopt( $ch, CURLOPT_VERBOSE, 			1 );
			curl_setopt( $ch, CURLOPT_FAILONERROR, 		1 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 			10 );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 	0 );
			
			$result = curl_exec( $ch );
			
			//echo "DEBUG: <b>". __CLASS__.":".__LINE__."</b>";
			//var_dump( $target_url, $feed_url, $result );
			
			return $result;
		}
		else 
		{
			global $ESS_Notices;
			$ESS_Notices->add_error( 
				__( "PHP cURL must be installed on your server: ", 'dbem' ) .
				ESS_Elements::get_curl_lib_link()
			);	
		}
		return false;
	}
	
	public static function set_event_saved_filter( $result=NULL, $event_id=NULL )
	{
		if ( $result == true )
		{
			if ( empty( $event_id ) )	
				global $EM_Event;
			else 
				$EM_Event = em_get_event( $event_id );
			
			//echo "DEBUG: <b>". __CLASS__.":".__LINE__."</b><br/>";
			//var_dump( $EM_Event );die;
				
			if ( $EM_Event instanceof EM_Event && intval( $EM_Event->event_id ) > 0 )
			{
				return ( self::get_curl_result( FeedWriter::$AGGREGATOR_WS, self::get_feed_url( $EM_Event->event_id, false, true ) ) !== false );
			}
		}
		return false;
	}
	
}
