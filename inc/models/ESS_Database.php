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
	
	const FEED_TABLE_NAME		= 'em_ess_feeds';
	
	// -- Feed_Status
	const FEED_STATUS_DELETED 	= 'DELETED';
	const FEED_STATUS_ACTIVE	= 'ACTIVE'; 
	
	// -- Feed_Modes
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
		self::init();
	}
	
	private static function init()
	{
		if ( strlen( self::$table ) <= 0 ) 
		{
			global $wpdb;
			if ( !isset( $wpdb ) ) $wpdb = $GLOBALS[ 'wpdb' ];
			self::$wpdb = $wpdb;
			
			self::$wpdb->show_errors();
			
			if ( EM_MS_GLOBAL )	$prefix = self::$wpdb->base_prefix;
			else 				$prefix = self::$wpdb->prefix;
			
			self::$table = $prefix . self::FEED_TABLE_NAME;
		}
	}
	
	private static function add_error( $errors )
	{
		global $ESS_Notices;
		$ESS_Notices->add_error( $errors );
	}
	
	
	public static function createTable() 
	{
		self::init();
		
		$sql = "CREATE TABLE " . self::$table . " (
			feed_id 		bigint( 20 ) 	UNSIGNED 									NOT NULL AUTO_INCREMENT,
			feed_owner	 	bigint( 20 ) 	UNSIGNED 									NOT NULL,
			feed_uuid		VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_event_ids 	VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_post_ids 	VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_title		VARCHAR( 256 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_host		VARCHAR( 128 ) 	CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_url 		VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci 	NOT NULL,
			feed_status		ENUM('".self::FEED_STATUS_ACTIVE."','".self::FEED_STATUS_DELETED."') 	CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '".self::FEED_STATUS_ACTIVE."',
			feed_mode		ENUM('".self::FEED_MODE_STANDALONE."','".self::FEED_MODE_CRON."') 		CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '".self::FEED_MODE_STANDALONE."',
			feed_timestamp	DATETIME 																NOT NULL,
			PRIMARY KEY (feed_id),
			UNIQUE  KEY `feed_uuid`  (`feed_uuid`),
					KEY `feed_owner` (`feed_owner`)
		) CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		dbDelta( $sql );
	}
	
	public static function deteteTable()
	{
		self::init();
		
		$sql = 'DROP TABLE ' . self::$table ;
		
		return ( ( self::$wpdb->query( $sql ) === false )? false : true );
	}
	
	public static function get( Array $DATA_=NULL )
	{
		self::init();
		
		$result = false;
		
		if ( @count( $DATA_ ) > 0 )
		{
			$sql = 
			" SELECT * " .
			" FROM " . self::$table . 
			" WHERE " . 
			(( isset( $DATA_['feed_status'] 	) )? " 		feed_status		= ".self::$wpdb->prepare( "%s", $DATA_['feed_status'] 	 ) : " ( feed_status = '".self::FEED_STATUS_ACTIVE."' OR feed_status = '".self::FEED_STATUS_DELETED."' ) " ) . 
			(( isset( $DATA_['feed_owner'] 		) )? " AND 	feed_owner		= ".self::$wpdb->prepare( "%d", $DATA_['feed_owner'] 	 ) : "" ) .
			(( isset( $DATA_['feed_uuid']	 	) )? " AND 	feed_uuid 		= ".self::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 	 ) : "" ) .
			(( isset( $DATA_['feed_event_ids'] 	) )? " AND 	feed_event_ids	= ".self::$wpdb->prepare( "%s", $DATA_['feed_event_ids'] ) : "" ) .
			(( isset( $DATA_['feed_post_ids'] 	) )? " AND 	feed_post_ids	= ".self::$wpdb->prepare( "%s", $DATA_['feed_post_ids']  ) : "" ) .
			(( isset( $DATA_['feed_title'] 		) )? " AND 	feed_title		= ".self::$wpdb->prepare( "%s", $DATA_['feed_title'] 	 ) : "" ) .
			(( isset( $DATA_['feed_host'] 		) )? " AND 	feed_host		= ".self::$wpdb->prepare( "%s", $DATA_['feed_host'] 	 ) : "" ) .
			(( isset( $DATA_['feed_url'] 		) )? " AND 	feed_url		= ".self::$wpdb->prepare( "%s", $DATA_['feed_url'] 		 ) : "" ) .
			(( isset( $DATA_['feed_mode'] 		) )? " AND 	feed_mode		= ".self::$wpdb->prepare( "%s", $DATA_['feed_mode'] 	 ) : "" );
			
			//self::add_error( $sql );
			
			$result = self::$wpdb->get_results( $sql, OBJECT_K );
		}	
		return $result;	
	}
	
	public static function add( Array $DATA_=NULL )
	{
		self::init();
		
		if ( !empty( $DATA_ ) )
		{
			$sql = "INSERT INTO " . self::$table . //IGNORE
			" ( ".
				(( intval( @$DATA_['feed_id']    ) > 0 )? "feed_id," 		: "" ) .	
				(( intval( @$DATA_['feed_owner'] ) > 0 )? "feed_owner," 	: "" ) .
				(( isset(  $DATA_['feed_uuid'] 		 ) )? "feed_uuid," 		: "" ) .
				(( isset(  $DATA_['feed_event_ids']  ) )? "feed_event_ids," : "" ) .
				(( isset(  $DATA_['feed_post_ids'] 	 ) )? "feed_post_ids," 	: "" ) .
				(( isset(  $DATA_['feed_title'] 	 ) )? "feed_title," 	: "" ) .
				(( isset(  $DATA_['feed_host'] 		 ) )? "feed_host," 		: "" ) .
				(( isset(  $DATA_['feed_url'] 		 ) )? "feed_url," 		: "" ) .
				(( isset(  $DATA_['feed_status'] 	 ) )? "feed_status," 	: "" ) . 
				(( isset(  $DATA_['feed_mode'] 		 ) )? "feed_mode," 		: "" ) .
												 		  "feed_timestamp".
			" ) VALUES ( " .
				(( intval( @$DATA_['feed_id']    ) > 0 )? self::$wpdb->prepare( "%d", $DATA_['feed_id'] 		) . "," : "" ) .	
				(( intval( @$DATA_['feed_owner'] ) > 0 )? self::$wpdb->prepare( "%d", $DATA_['feed_owner'] 	 	) . "," : "" ) .
				(( isset(  $DATA_['feed_uuid'] 		 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 		) . "," : "" ) .
				(( isset(  $DATA_['feed_event_ids']  ) )? self::$wpdb->prepare( "%s", $DATA_['feed_event_ids'] 	) . "," : "" ) .
				(( isset(  $DATA_['feed_post_ids'] 	 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_post_ids']  	) . "," : "" ) .
				(( isset(  $DATA_['feed_title'] 	 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_title'] 	 	) . "," : "" ) .
				(( isset(  $DATA_['feed_host'] 		 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_host'] 	 	) . "," : "" ) .
				(( isset(  $DATA_['feed_url'] 		 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_url'] 		) . "," : "" ) .
				(( isset(  $DATA_['feed_status'] 	 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_status'] 	) . "," : "" ) .
				(( isset(  $DATA_['feed_mode'] 		 ) )? self::$wpdb->prepare( "%s", $DATA_['feed_mode'] 	 	) . "," : "" ) .
														  "'".date("Y-m-d H:i:s")."' " .
			" ) " .
			(( intval( @$DATA_['feed_id'] ) > 0 )? 
				" ON DUPLICATE KEY UPDATE ".
														 " feed_id			= " . self::$wpdb->prepare( "%d", $DATA_['feed_id'] 		) .
				(( isset( $DATA_['feed_owner'] 		) )? ",feed_owner		= " . self::$wpdb->prepare( "%d", $DATA_['feed_owner'] 		) : "" ) .
				(( isset( $DATA_['feed_uuid']	 	) )? ",feed_uuid 		= " . self::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 		) : "" ) .
				(( isset( $DATA_['feed_event_ids'] 	) )? ",feed_event_ids	= " . self::$wpdb->prepare( "%s", $DATA_['feed_event_ids'] 	) : "" ) .
				(( isset( $DATA_['feed_post_ids'] 	) )? ",feed_post_ids	= " . self::$wpdb->prepare( "%s", $DATA_['feed_post_ids'] 	) : "" ) .
				(( isset( $DATA_['feed_title'] 		) )? ",feed_title		= " . self::$wpdb->prepare( "%s", $DATA_['feed_title'] 		) : "" ) .
				(( isset( $DATA_['feed_host'] 		) )? ",feed_host		= " . self::$wpdb->prepare( "%s", $DATA_['feed_host'] 		) : "" ) .
				(( isset( $DATA_['feed_url'] 		) )? ",feed_url			= " . self::$wpdb->prepare( "%s", $DATA_['feed_url'] 		) : "" ) .
				(( isset( $DATA_['feed_status'] 	) )? ",feed_status		= " . self::$wpdb->prepare( "%s", $DATA_['feed_status'] 	) : "" ) . 
				(( isset( $DATA_['feed_mode'] 		) )? ",feed_mode		= " . self::$wpdb->prepare( "%s", $DATA_['feed_mode'] 		) : "" ) .
														 ",feed_timestamp	= '". date("Y-m-d H:i:s")."' " 
				:
				""
			);
			
			//self::add_error( $sql );
			
			return ( ( self::$wpdb->query( $sql ) === false )? false : true );
		}
		
		//self::add_error( self::$wpdb->last_error );
		
		return false;
	}
	
	public static function count( Array $DATA_=NULL )
	{
		self::init();
		
		$sql = "".
		" SELECT COUNT(*) " .
		" FROM ". self::$table . 
		" WHERE " .
		(( isset( $DATA_['feed_status'] 	) )? " 		feed_status		= " . self::$wpdb->prepare( "%s", $DATA_['feed_status'] 	) : " feed_status='".self::FEED_STATUS_ACTIVE."'" ) . 
		(( isset( $DATA_['feed_owner'] 		) )? " AND 	feed_owner		= " . self::$wpdb->prepare( "%d", $DATA_['feed_owner']	 	) : "" ) .
		(( isset( $DATA_['feed_uuid']	 	) )? " AND 	feed_uuid 		= " . self::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 		) : "" ) .
		(( isset( $DATA_['feed_event_ids'] 	) )? " AND 	feed_event_ids	= " . self::$wpdb->prepare( "%s", $DATA_['feed_event_ids']	) : "" ) .
		(( isset( $DATA_['feed_post_ids'] 	) )? " AND 	feed_post_ids	= " . self::$wpdb->prepare( "%s", $DATA_['feed_post_ids'] 	) : "" ) .
		(( isset( $DATA_['feed_title'] 		) )? " AND 	feed_title		= " . self::$wpdb->prepare( "%s", $DATA_['feed_title'] 		) : "" ) .
		(( isset( $DATA_['feed_host'] 		) )? " AND 	feed_host		= " . self::$wpdb->prepare( "%s", $DATA_['feed_host'] 		) : "" ) .
		(( isset( $DATA_['feed_url'] 		) )? " AND 	feed_url		= " . self::$wpdb->prepare( "%s", $DATA_['feed_url'] 		) : "" ) .
		(( isset( $DATA_['feed_mode'] 		) )? " AND 	feed_mode		= " . self::$wpdb->prepare( "%s", $DATA_['feed_mode'] 		) : "" );
				
		//self::add_error( $sql );
		
		return self::$wpdb->get_var( $sql );
	}
	
	public static function delete( Array $DATA_=NULL )
	{
		self::init();
		
		$sql = "".
		" DELETE " .
		" FROM ". self::$table . 
		" WHERE " .
		(( isset( $DATA_['feed_status'] ) )? " 		feed_status		= " . self::$wpdb->prepare( "%s", $DATA_['feed_status'] ) : " feed_status='".self::FEED_STATUS_ACTIVE."'" ) . 
		(( isset( $DATA_['feed_id'] 	) )? " AND	feed_id			= " . self::$wpdb->prepare( "%d", $DATA_['feed_id'] 	) : "" ) .
		(( isset( $DATA_['feed_owner'] 	) )? " AND 	feed_owner		= " . self::$wpdb->prepare( "%d", $DATA_['feed_owner']	) : "" ) .
		(( isset( $DATA_['feed_uuid']	) )? " AND 	feed_uuid 		= " . self::$wpdb->prepare( "%s", $DATA_['feed_uuid'] 	) : "" ) .
		(( isset( $DATA_['feed_title'] 	) )? " AND 	feed_title		= " . self::$wpdb->prepare( "%s", $DATA_['feed_title']  ) : "" ) .
		(( isset( $DATA_['feed_host'] 	) )? " AND 	feed_host		= " . self::$wpdb->prepare( "%s", $DATA_['feed_host'] 	) : "" ) .
		(( isset( $DATA_['feed_mode'] 	) )? " AND 	feed_mode		= " . self::$wpdb->prepare( "%s", $DATA_['feed_mode'] 	) : "" );
		
		//self::add_error( $sql );	
			
		return ( ( self::$wpdb->query( $sql ) === false )? false : true );
	}
	
	public static function clear_locations()
	{
		self::init();
		
		$sql = " DELETE FROM ". EM_LOCATIONS_TABLE . 
		" WHERE ( 	location_address	IS NULL OR location_address = '' ) ".
		" AND (		location_town		IS NULL OR location_town	= '' ) ".
		" AND (		location_state		IS NULL OR location_state	= '' ) ". 
		" AND (		location_postcode	IS NULL OR location_postcode= '' ) ".
		" AND (		location_region		IS NULL OR location_region	= '' ) ".
		" AND (		location_country	IS NULL OR location_country	= '' ) ";
		
		//self::add_error( $sql );
		
		return ( ( self::$wpdb->query( $sql ) === false )? false : true );
	}
	
	
	
	public static function set_default_values()
	{
		$user = wp_get_current_user();
		
		$l = get_bloginfo('language');
		$language = strtolower( $l{0}.$l{1} );
			
		$ess_options = array(
			
			// -- Feed Settings 
			'ess_feed_title' 			=> sprintf( __( 'Events from: %s', 'dbem' ), @$_SERVER[ 'HTTP_HOST' ] ),
			'ess_feed_rights'			=> sprintf( __( '© ESS | Events Manager %s', 'dbem' ), date('Y') ),
			'ess_feed_website'			=> ESS_IO::HTTP . $_SERVER['SERVER_NAME'],
			'ess_feed_category_type' 	=> self::DEFAULT_CATEGORY_TYPE,
			'ess_feed_language'			=> ( ( strlen( $language ) == 2 )? $language : self::DEFAULT_LANGUAGE ),
			'ess_feed_currency' 		=> get_option( 'dbem_bookings_currency', self::DEFAULT_CURRENCY ), 
			'ess_feed_limit'			=> 100,
			'ess_feed_timezone'			=> ESS_Timezone::get_default_timezone(),
			
			// -- Feed Visibility
				// -- Global
				'ess_feed_visibility_web' 	=> true,
				'ess_feed_visibility_meta'	=> true,
				'ess_feed_push'				=> true,
				// -- Feed's Elements
				'ess_feed_import_images'	=> false,
				'ess_feed_export_images'	=> true,
				'ess_feed_import_videos'	=> false,
				'ess_feed_export_videos'	=> true,
				'ess_feed_import_sounds'	=> false,
				'ess_feed_export_sounds'	=> true,
			
			
			// -- Event Organizer
			'ess_owner_activate' 	=> false,
				'ess_owner_firstname' 	=> $user->data->user_nicename,
				'ess_owner_lastname' 	=> '',
				'ess_owner_company' 	=> '',
				'ess_owner_address' 	=> '',
				'ess_owner_city' 		=> @$_SERVER['GEOIP_CITY'],  // if mod_geip is installed on the server...
				'ess_owner_state' 		=> @$_SERVER['GEOIP_REGION_NAME'],
				'ess_owner_zip' 		=> @$_SERVER['GEOIP_POSTAL_CODE'],
				'ess_owner_country' 	=> ((strlen(get_option('dbem_location_default_country'))>0)? get_option('dbem_location_default_country') : @$_SERVER['GEOIP_COUNTRY_CODE'] ),
				'ess_owner_email' 		=> $user->data->user_email,
				'ess_owner_website' 	=> ((strlen($user->data->user_url)>0)?$user->data->user_url:ESS_IO::HTTP.$_SERVER['SERVER_NAME']),
				'ess_owner_phone'		=> $user->data->phone
		);
		
		// -- Social Platforms
		foreach ( self::$SOCIAL_PLATFORMS as $type => $socials_ )
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
			return true;
		}
		return false;
	}
	
	public static function get_option( $name=NULL, $default='' )
	{
		return ( $name !== NULL )? stripcslashes( strip_tags( esc_html( ( strlen( get_option( $name ) ) > 0 )? get_option( $name, $default ) : $default ) ) ) : $default;
	}
	
	public static function update_feeds_daily() 
	{
		$feeds_ = self::get( array( 'feed_mode' => self::FEED_MODE_CRON ) );
		
		mail( 'brice@peach.fr', 'Wordpress CRON Update', @count( $feeds_ ).' feeds have been updated' );
		
		if ( @count( $feeds_ ) > 0 )
		{
			foreach ( $feeds_ as $feed ) 
			{
				ESS_Import::save( $feed->feed_url, 'on' );
			}
		}
	}
	
}