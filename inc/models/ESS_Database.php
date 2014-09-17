<?php
/**
  * Model ESS_Database
  * Container of the interface with the database
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */
final class ESS_Database
{
	private static $wpdb 	= NULL;
	private static $table 	= "";
	public static $sql 		= "";

	const FEED_TABLE_NAME		= 'em_ess_feeds';

	// -- Syndication Status
	const EVENT_STATUS_DRAFT	= 'draft';
	const EVENT_STATUS_PUBLISH	= 'publish';

	// -- Feed Status
	const FEED_STATUS_DELETED 	= 'DELETED';
	const FEED_STATUS_ACTIVE	= 'ACTIVE';

	// -- Feed Modes
	const FEED_MODE_STANDALONE 	= 'STANDALONE';
	const FEED_MODE_CRON		= 'CRON';

	// -- Default Feed Attributes
	const DEFAULT_LANGUAGE		= 'en';		 // ISO 4217 language code (2 chars), default 'en'
	const DEFAULT_CURRENCY		= 'USD';
	const DEFAULT_CATEGORY_TYPE = 'general';

	// -- Social Plateforms
	public static $SOCIAL_PLATFORMS = array(
		'friends' 	=> array( 'facebook', 'twitter', 'googleplus', 'orkut', 'reddit' ),
		'events' 	=> array( 'foursquare', 'eventbrite', 'hypecal', 'meetup', 'eventful' ),
		'music' 	=> array( 'soundcloud', 'lastfm', 'spotify', 'myspace', 'songkick' ),
		'images'	=> array( 'tumblr', 'instagram', 'pinterest', 'flickr'  ),
		'videos' 	=> array( 'youtube', 'vimeo' )
	);

	var $feed_id;
	var $feed_host;
	var $feed_url;
	var $feed_status;
	var $feed_mode;
	var $feed_timestamp;


	public function __construct()
	{
		ESS_Database::init();
	}

	public static function init()
	{
		if ( strlen( ESS_Database::$table ) <= 0 )
		{
			global $wpdb;
			if ( !isset( $wpdb ) ) $wpdb = $GLOBALS[ 'wpdb' ];
			ESS_Database::$wpdb = $wpdb;

			ESS_Database::$wpdb->show_errors();

			if ( EM_MS_GLOBAL )	$prefix = ESS_Database::$wpdb->base_prefix;
			else 				$prefix = ESS_Database::$wpdb->prefix;

			ESS_Database::$table = $prefix . ESS_Database::FEED_TABLE_NAME;
		}
	}

	public static function add_error( $errors )
	{
		global $ESS_Notices;

		$ESS_Notices->add_error( $errors );
	}


	public static function createTable()
	{
		ESS_Database::init();

		if ( @count( ESS_Database::$wpdb->get_results( "SHOW TABLES LIKE '".ESS_Database::$table."';" ) ) >= 1 )
			return;

		ESS_Database::$sql = "CREATE TABLE " . ESS_Database::$table . " (
			feed_id 		bigint( 20 ) 	UNSIGNED 									NOT NULL AUTO_INCREMENT,
			feed_owner	 	bigint( 20 ) 	UNSIGNED 									NOT NULL,
			feed_uuid		VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_event_ids 	VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_post_ids 	VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_title		VARCHAR( 256 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_host		VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_url 		VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_status		ENUM('".ESS_Database::FEED_STATUS_ACTIVE."','".ESS_Database::FEED_STATUS_DELETED."') 	CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '".ESS_Database::FEED_STATUS_ACTIVE."',
			feed_mode		ENUM('".ESS_Database::FEED_MODE_STANDALONE."','".ESS_Database::FEED_MODE_CRON."') 		CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '".ESS_Database::FEED_MODE_STANDALONE."',
			feed_timestamp	DATETIME 													NOT NULL,
			PRIMARY KEY (feed_id),
			UNIQUE  KEY `feed_uuid`  (`feed_uuid`),
					KEY `feed_owner` (`feed_owner`)
		) CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( ESS_Database::$sql );
	}

	public static function deteteTable()
	{
		ESS_Database::init();

		ESS_Database::$sql = 'DROP TABLE ' . ESS_Database::$table ;

		return ( ( ESS_Database::$wpdb->query( ESS_Database::$sql ) === FALSE )? FALSE : TRUE );
	}

	public static function get( Array $DATA_=NULL )
	{
		ESS_Database::init();

		$result = FALSE;

		if ( @count( $DATA_ ) > 0 )
		{
			ESS_Database::$sql =
				" SELECT * " .
				" FROM " . ESS_Database::$table .
				" WHERE " .
				( ( isset( $DATA_['feed_status'] 	) )? " 		feed_status		= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_status'] 	 ) : " ( feed_status = '".ESS_Database::FEED_STATUS_ACTIVE."' OR feed_status = '".ESS_Database::FEED_STATUS_DELETED."' ) " ) .
				( ( isset( $DATA_['feed_owner'] 	) )? " AND 	feed_owner		= ".ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_owner'] 	 ) : "" ) .
				( ( isset( $DATA_['feed_uuid']	 	) )? " AND 	feed_uuid 		= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 	 ) : "" ) .
				( ( isset( $DATA_['feed_event_ids'] ) )? " AND 	feed_event_ids	= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_event_ids'] ) : "" ) .
				( ( isset( $DATA_['feed_post_ids'] 	) )? " AND 	feed_post_ids	= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_post_ids']  ) : "" ) .
				( ( isset( $DATA_['feed_title'] 	) )? " AND 	feed_title		= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_title'] 	 ) : "" ) .
				( ( isset( $DATA_['feed_host'] 		) )? " AND 	feed_host		= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_host'] 	 ) : "" ) .
				( ( isset( $DATA_['feed_url'] 		) )? " AND 	feed_url		= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_url'] 		 ) : "" ) .
				( ( isset( $DATA_['feed_mode'] 		) )? " AND 	feed_mode		= ".ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_mode'] 	 ) : "" );

			//ESS_Database::add_error( $sql );

			$result = ESS_Database::$wpdb->get_results( ESS_Database::$sql, OBJECT_K );
		}
		return $result;
	}

	public static function add( Array $DATA_=NULL )
	{
		ESS_Database::init();

		if ( !empty( $DATA_ ) )
		{
			ESS_Database::$sql = "INSERT INTO " . ESS_Database::$table . //IGNORE
			" ( ".
				( ( intval( @$DATA_['feed_id']    		) > 0 )? "feed_id," 		: "" ) .
				( ( intval( @$DATA_['feed_owner'] 		) > 0 )? "feed_owner," 		: "" ) .
				( ( isset(  $DATA_['feed_uuid'] 			) )? "feed_uuid," 		: "" ) .
				( ( isset(  $DATA_['feed_event_ids'] 		) )? "feed_event_ids," 	: "" ) .
				( ( isset(  $DATA_['feed_post_ids'] 		) )? "feed_post_ids," 	: "" ) .
				( ( isset(  $DATA_['feed_title'] 	 		) )? "feed_title," 		: "" ) .
				( ( isset(  $DATA_['feed_host'] 		 	) )? "feed_host," 		: "" ) .
				( ( isset(  $DATA_['feed_url'] 		 		) )? "feed_url," 		: "" ) .
				( ( isset(  $DATA_['feed_status'] 	 		) )? "feed_status," 	: "" ) .
				( ( isset(  $DATA_['feed_mode'] 		 	) )? "feed_mode," 		: "" ) .
												 		 	 	 "feed_timestamp".
			" ) VALUES ( " .
				( ( intval( @$DATA_['feed_id']    	) > 0 )? ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_id'] 			) . "," : "" ) .
				( ( intval( @$DATA_['feed_owner'] 	) > 0 )? ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_owner'] 	 	) . "," : "" ) .
				( ( isset(  $DATA_['feed_uuid'] 		) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 		) . "," : "" ) .
				( ( isset(  $DATA_['feed_event_ids']  	) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_event_ids'] 	) . "," : "" ) .
				( ( isset(  $DATA_['feed_post_ids'] 	) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_post_ids']  	) . "," : "" ) .
				( ( isset(  $DATA_['feed_title'] 		) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_title'] 	 	) . "," : "" ) .
				( ( isset(  $DATA_['feed_host'] 		) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_host'] 	 	) . "," : "" ) .
				( ( isset(  $DATA_['feed_url'] 		 	) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_url'] 		) . "," : "" ) .
				( ( isset(  $DATA_['feed_status'] 	 	) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_status'] 		) . "," : "" ) .
				( ( isset(  $DATA_['feed_mode'] 		) )? ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_mode'] 	 	) . "," : "" ) .
														  "'".date( "Y-m-d H:i:s" )."' " .
			" ) " .
			(( intval( @$DATA_['feed_id'] ) > 0 )?
				" ON DUPLICATE KEY UPDATE ".
														 " feed_id			= " . ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_id'] 		) .
				(( isset( $DATA_['feed_owner'] 		) )? ",feed_owner		= " . ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_owner'] 		) : "" ) .
				(( isset( $DATA_['feed_uuid']	 	) )? ",feed_uuid 		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 		) : "" ) .
				(( isset( $DATA_['feed_event_ids'] 	) )? ",feed_event_ids	= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_event_ids'] 	) : "" ) .
				(( isset( $DATA_['feed_post_ids'] 	) )? ",feed_post_ids	= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_post_ids'] 	) : "" ) .
				(( isset( $DATA_['feed_title'] 		) )? ",feed_title		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_title'] 		) : "" ) .
				(( isset( $DATA_['feed_host'] 		) )? ",feed_host		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_host'] 		) : "" ) .
				(( isset( $DATA_['feed_url'] 		) )? ",feed_url			= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_url'] 		) : "" ) .
				(( isset( $DATA_['feed_status'] 	) )? ",feed_status		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_status'] 	) : "" ) .
				(( isset( $DATA_['feed_mode'] 		) )? ",feed_mode		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_mode'] 		) : "" ) .
														 ",feed_timestamp	= '". date("Y-m-d H:i:s")."' "
				:
				""
			);

			//ESS_Database::add_error( ESS_Database::$sql );

			return ( ( ESS_Database::$wpdb->query( ESS_Database::$sql ) === FALSE )? FALSE : TRUE );
		}

		//ESS_Database::add_error( ESS_Database::$wpdb->last_error );

		return FALSE;
	}

	public static function count( Array $DATA_=NULL )
	{
		ESS_Database::init();

		ESS_Database::$sql =
			" SELECT COUNT(*) " .
			" FROM ". ESS_Database::$table .
			" WHERE " .
			(( isset( $DATA_['feed_status'] 	) )? " 		feed_status		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_status'] 	) : " feed_status='".ESS_Database::FEED_STATUS_ACTIVE."'" ) .
			(( isset( $DATA_['feed_owner'] 		) )? " AND 	feed_owner		= " . ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_owner']	 	) : "" ) .
			(( isset( $DATA_['feed_uuid']	 	) )? " AND 	feed_uuid 		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 		) : "" ) .
			(( isset( $DATA_['feed_event_ids'] 	) )? " AND 	feed_event_ids	= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_event_ids']	) : "" ) .
			(( isset( $DATA_['feed_post_ids'] 	) )? " AND 	feed_post_ids	= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_post_ids'] 	) : "" ) .
			(( isset( $DATA_['feed_title'] 		) )? " AND 	feed_title		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_title'] 		) : "" ) .
			(( isset( $DATA_['feed_host'] 		) )? " AND 	feed_host		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_host'] 		) : "" ) .
			(( isset( $DATA_['feed_url'] 		) )? " AND 	feed_url		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_url'] 		) : "" ) .
			(( isset( $DATA_['feed_mode'] 		) )? " AND 	feed_mode		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_mode'] 		) : "" );

		//ESS_Database::add_error( ESS_Database::$sql );

		return ESS_Database::$wpdb->get_var( ESS_Database::$sql );
	}

	public static function delete( Array $DATA_=NULL )
	{
		ESS_Database::init();

		ESS_Database::$sql =
			" DELETE " .
			" FROM ". ESS_Database::$table .
			" WHERE " .
			(( isset( $DATA_['feed_status'] ) )? " 		feed_status		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_status'] ) : " feed_status='".ESS_Database::FEED_STATUS_ACTIVE."'" ) .
			(( isset( $DATA_['feed_id'] 	) )? " AND	feed_id			= " . ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_id'] 	) : "" ) .
			(( isset( $DATA_['feed_owner'] 	) )? " AND 	feed_owner		= " . ESS_Database::$wpdb->prepare( "%d", $DATA_['feed_owner']	) : "" ) .
			(( isset( $DATA_['feed_uuid']	) )? " AND 	feed_uuid 		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 	) : "" ) .
			(( isset( $DATA_['feed_title'] 	) )? " AND 	feed_title		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_title']  ) : "" ) .
			(( isset( $DATA_['feed_host'] 	) )? " AND 	feed_host		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_host'] 	) : "" ) .
			(( isset( $DATA_['feed_mode'] 	) )? " AND 	feed_mode		= " . ESS_Database::$wpdb->prepare( "%s", $DATA_['feed_mode'] 	) : "" );

		//ESS_Database::add_error( ESS_Database::$sql );

		return ( ( ESS_Database::$wpdb->query( ESS_Database::$sql ) === FALSE )? FALSE : TRUE );
	}

	public static function clear_locations()
	{
		ESS_Database::init();

		ESS_Database::$sql =
			"DELETE FROM ". EM_LOCATIONS_TABLE .
			" WHERE ( 	location_address	IS NULL OR location_address = '' ) ".
			" AND (		location_town		IS NULL OR location_town	= '' ) ".
			" AND (		location_state		IS NULL OR location_state	= '' ) ".
			" AND (		location_postcode	IS NULL OR location_postcode= '' ) ".
			" AND (		location_region		IS NULL OR location_region	= '' ) ".
			" AND (		location_country	IS NULL OR location_country	= '' ) ";

		//ESS_Database::add_error( ESS_Database::$sql );

		return ( ( ESS_Database::$wpdb->query( ESS_Database::$sql ) === FALSE )? FALSE : TRUE );
	}



	public static function set_default_values()
	{
		$user = wp_get_current_user();

		$l = get_bloginfo( 'language' );
		$language = strtolower( $l{0}.$l{1} );

		$ess_options = array(

			// -- Syndication Settings
			'ess_syndication_status' 	=> FALSE, // FALSE => 'draft'  ||  TRUE => 'publish'
			'ess_backlink_enabled' 		=> FALSE,

			// -- Feed Settings
			'ess_feed_title' 			=> sprintf( __( 'Events from: %s', 'dbem' ), @$_SERVER[ 'HTTP_HOST' ] ),
			'ess_feed_rights'			=> sprintf( __( '© ESS | Events Manager %s', 'dbem' ), date( 'Y' ) ),
			'ess_feed_website'			=> ESS_IO::HTTP . $_SERVER['SERVER_NAME'],
			'ess_feed_category_type' 	=> ESS_Database::DEFAULT_CATEGORY_TYPE,
			'ess_feed_language'			=> ( ( strlen( $language ) == 2 )? $language : ESS_Database::DEFAULT_LANGUAGE ),
			'ess_feed_currency' 		=> get_option( 'dbem_bookings_currency', ESS_Database::DEFAULT_CURRENCY ),
			'ess_feed_limit'			=> 200,
			'ess_feed_timezone'			=> ( ( class_exists( 'ESS_Timezone' ) )? ESS_Timezone::get_default_timezone() : '' ),

			// -- Feed Visibility
				// -- Global
				'ess_feed_visibility_web' 	=> TRUE,
				'ess_feed_visibility_meta'	=> TRUE,
				'ess_feed_push'				=> TRUE,
				// -- Feed's Elements
				'ess_feed_import_images'	=> FALSE,
				'ess_feed_export_images'	=> TRUE,
				'ess_feed_import_videos'	=> FALSE,
				'ess_feed_export_videos'	=> TRUE,
				'ess_feed_import_sounds'	=> FALSE,
				'ess_feed_export_sounds'	=> TRUE,


			// -- Event Organizer
			'ess_owner_activate' 	=> FALSE,
				'ess_owner_firstname' 	=> ( ( isset( $user->data ) )? ( ( isset( $user->data->user_nicename ) )? $user->data->user_nicename : '' ) : '' ),
				'ess_owner_lastname' 	=> '',
				'ess_owner_company' 	=> '',
				'ess_owner_address' 	=> '',
				'ess_owner_city' 		=> @$_SERVER[ 'GEOIP_CITY'			], // if mod_geip is installed on the server...
				'ess_owner_state' 		=> @$_SERVER[ 'GEOIP_REGION_NAME'	],
				'ess_owner_zip' 		=> @$_SERVER[ 'GEOIP_POSTAL_CODE'	],
				'ess_owner_country' 	=> ( ( strlen(get_option( 'dbem_location_default_country' ) ) > 0 )? get_option('dbem_location_default_country') : @$_SERVER['GEOIP_COUNTRY_CODE'] ),
				'ess_owner_email' 		=> ( ( isset( $user->data ) )? ( ( isset( $user->data->user_email 	) )? $user->data->user_email 	: '' 									) : '' ),
				'ess_owner_website' 	=> ( ( isset( $user->data ) )? ( ( isset( $user->data->user_url 	) )? $user->data->user_url 		: ESS_IO::HTTP.$_SERVER['SERVER_NAME'] 	) : ESS_IO::HTTP.$_SERVER['SERVER_NAME'] ),
				'ess_owner_phone'		=> ( ( isset( $user->data ) )? ( ( isset( $user->data->phone 		) )? $user->data->phone 		: '' 									) : '' )
		);

		// -- Social Platforms
		foreach ( ESS_Database::$SOCIAL_PLATFORMS as $type => $socials_ )
		{
			foreach ( $socials_ as $social )
				$ess_options[ 'ess_social_'.$social ] = ESS_IO::HTTP;
		}

		foreach( $ess_options as $key => $value )
			add_option( $key, $value );
	}

	public static function set_option( $name=NULL, $value=NULL )
	{
		if ( $name !== NULL && $value !== NULL )
		{
			update_option( $name, $value );
			return TRUE;
		}
		return FALSE;
	}

	public static function get_option( $name=NULL, $default='' )
	{
		return ( $name !== NULL )? stripcslashes( strip_tags( esc_html( ( strlen( get_option( $name ) ) > 0 )? get_option( $name, $default ) : $default ) ) ) : $default;
	}

	public static function update_feeds_daily()
	{
		 $headers  = 'MIME-Version: 1.0' . "\r\n";
    	 $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		$feeds_ = ESS_Database::get( array( 'feed_mode' => ESS_Database::FEED_MODE_CRON ) );

		if ( @count( $feeds_ ) > 0 && $feeds_ != NULL )
		{
			foreach ( $feeds_ as $feed )
			{
				ESS_Import::save( $feed->feed_url, 'on' );
				//mail( 'brice@peach.fr', 'DAILY count: '.@count( $feeds_ ), 'feed URL: '. $feed->feed_url . '<br><br>_POST:' . @htmlvardump( $_POST ) . '<br><br>POST_ : ' . @htmlvardump( ESS_Import::$POST_ ), $headers );
			}
		}
	}

	public static function update_feeds_hourly()
	{
		//mail( 'brice@peach.fr', 'test: '.@get_bloginfo( 'admin_email' ), 'test: '.@current_time( 'mysql' ) );

		//error_log( "Function do_cron() executed.");

		//$feeds_ = ESS_Database::get( array( 'feed_mode' => ESS_Database::FEED_MODE_CRON ) );

		//mail( 'brice@peach.fr', 'HOURLY count: '.@count( $feeds_ ), 'feed URL: '.htmlvardump( $feeds_ ) );

		ESS_Database::update_feeds_daily();
	}

}