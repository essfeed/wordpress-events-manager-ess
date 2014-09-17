<?php
/**
  * Controller ESS_Control_admin
  * Control the user interaction with Admin page
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */
final class ESS_Control_admin
{
	function __construct(){}



	public static function control_requirement()
	{
		// If Events Managers is not installed
		if ( @strlen( EM_DIR ) <= 0 )
			ESS_Elements::get_events_manager_required();

		// If PHP cURL not installed
		if ( function_exists( 'curl_version' ) == FALSE )
			ESS_Elements::get_php_curl_required();
	}

	public static function control_import_ESS()
	{
		$import_url			= ( isset( $_REQUEST[ 'ess_feed_url' ] 		) )? urldecode( @$_REQUEST[ 'ess_feed_url' ] 	) : '';
		$update_url 		= ( isset( $_REQUEST[ 'update_once' ] 		) )? urldecode( @$_REQUEST[ 'update_once' ] 	) : '';
		$selected_event_id 	= ( isset( $_REQUEST[ 'selected_event_id' ] ) )? intval( 	@$_REQUEST['selected_event_id'] ) : '';

		if ( (
				strlen( $import_url ) <= 0 || $import_url == ESS_IO::HTTP
			) && (
				FeedValidator::isValidURL( $update_url ) == FALSE || $selected_event_id <= 0
			)
		) return;

		if ( FeedValidator::isValidURL( $update_url ) && $selected_event_id > 0 )
			ESS_Import::save( $update_url, @$_REQUEST[ 'feed_mode_' . $selected_event_id ] );
		else
			ESS_Import::save( $import_url, @$_REQUEST[ 'ess_feed_mode' ] );
	}

	public static function control_nav_actions()
	{
		if ( !isset( $_REQUEST[ 'action' ] ) && !isset( $_REQUEST[ 'feeds' ] ) ) return;

		if ( @count( @$_REQUEST['feeds'] ) > 0 && strlen( $_REQUEST['action'] ) > 0 )
		{
			global $ESS_Notices;

			$count_action = 0;

			foreach ( $_REQUEST['feeds'] as $feed_id )
			{
				if ( intval( $feed_id ) > 0 )
				{
					$count_action++;

					switch ( $_REQUEST['action'] )
					{
						default 			: $action = __( 'have been updated', 					'dbem' ); break;
						case 'active' 		: $action = __( 'have been activated', 					'dbem' ); break;
						case 'deleted' 		: $action = __( 'have been deleted', 	 				'dbem' ); break;
						case 'full_deleted'	: $action = __( 'have been definitively removed', 		'dbem' ); break;
						case 'update_cron'	: $action = __( 'have its daily update reactualized', 	'dbem' ); break;
					}

					if ( $_REQUEST['action'] == 'active' ||
						 $_REQUEST['action'] == 'deleted' )
						ESS_Database::add( array(
							'feed_id'		=> $feed_id,
							'feed_status'	=> strtoupper( $_REQUEST[ 'action' ] )
						) );

					else if ( $_REQUEST['action'] == 'full_deleted' )
						ESS_Database::delete( array(
							'feed_status' 	=> ESS_Database::FEED_STATUS_DELETED,
							'feed_id'		=> $feed_id
						) );

					else if ( $_REQUEST['action'] == 'update_cron' )
					{
						$feed_mode = ( ( @$_REQUEST[ 'feed_mode_'.$feed_id ] == 'on' )?
							ESS_Database::FEED_MODE_CRON
							:
							ESS_Database::FEED_MODE_STANDALONE
						);

						ESS_Database::add( array(
							'feed_id'	=> $feed_id,
							'feed_mode'	=> $feed_mode
						) );
					}
				}
			}
			$ESS_Notices->add_confirm( sprintf( __( "%d rows %s.",'dbem'), $count_action, $action ) );
		}
	}

	public static function control_export_forms()
	{
		if ( !isset( $_REQUEST['save_export'] ) || empty( $_REQUEST['save_export'] ) ) return;

		global $ESS_Notices;

		// -- Syndication Settings
		ESS_Database::set_option( 'ess_syndication_status', 	( @$_REQUEST['ess_syndication_status'] 	== 'on' ) ? TRUE : FALSE );
		ESS_Database::set_option( 'ess_backlink_enabled', 		( @$_REQUEST['ess_backlink_enabled'] 	== 'on' ) ? TRUE : FALSE );

		// -- Feed Settings
		ESS_Database::set_option( 'ess_feed_title', 			@$_REQUEST['ess_feed_title'] 		);
		ESS_Database::set_option( 'ess_feed_rights', 			@$_REQUEST['ess_feed_rights'] 		);
		ESS_Database::set_option( 'ess_feed_website', 			@$_REQUEST['ess_feed_website'] 		);
		ESS_Database::set_option( 'ess_feed_limit', 			@$_REQUEST['ess_feed_limit'] 		);
		ESS_Database::set_option( 'ess_feed_category_type', 	@$_REQUEST['ess_feed_category_type']);
		ESS_Database::set_option( 'ess_feed_currency', 			@$_REQUEST['ess_feed_currency']		);
		ESS_Database::set_option( 'ess_feed_language', 			@$_REQUEST['ess_feed_language'] 	);
		ESS_Database::set_option( 'ess_feed_timezone', 			@$_REQUEST['ess_feed_timezone'] 	);

		// -- Feed Visibility
			// -- Global
			ESS_Database::set_option( 'ess_feed_visibility_web', 	( @$_REQUEST['ess_feed_visibility_web']  == 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_visibility_meta', 	( @$_REQUEST['ess_feed_visibility_meta'] == 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_push', 				( @$_REQUEST['ess_feed_push'] 			 == 'on' ) ? TRUE : FALSE );
			// Elements
			ESS_Database::set_option( 'ess_feed_import_images', 	( @$_REQUEST['ess_feed_import_images'] 	== 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_export_images', 	( @$_REQUEST['ess_feed_export_images'] 	== 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_import_videos', 	( @$_REQUEST['ess_feed_import_videos'] 	== 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_export_videos', 	( @$_REQUEST['ess_feed_export_videos'] 	== 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_import_sounds', 	( @$_REQUEST['ess_feed_import_sounds'] 	== 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_feed_export_sounds', 	( @$_REQUEST['ess_feed_export_sounds'] 	== 'on' ) ? TRUE : FALSE );

		// -- Event Organizer
		ESS_Database::set_option( 'ess_owner_activate', 		( @$_REQUEST['ess_owner_activate'] 	== 'on' ) ? TRUE : FALSE );
			ESS_Database::set_option( 'ess_owner_firstname', 		@$_REQUEST['ess_owner_firstname'] 		);
			ESS_Database::set_option( 'ess_owner_lastname', 		@$_REQUEST['ess_owner_lastname'] 		);
			ESS_Database::set_option( 'ess_owner_company', 			@$_REQUEST['ess_owner_company'] 		);
			ESS_Database::set_option( 'ess_owner_city', 			@$_REQUEST['ess_owner_city'] 			);
			ESS_Database::set_option( 'ess_owner_address', 			@$_REQUEST['ess_owner_address'] 		);
			ESS_Database::set_option( 'ess_owner_zip', 				@$_REQUEST['ess_owner_zip'] 			);
			ESS_Database::set_option( 'ess_owner_state', 			@$_REQUEST['ess_owner_state'] 			);
			ESS_Database::set_option( 'ess_owner_country', 			@$_REQUEST['ess_owner_country'] 		);
			ESS_Database::set_option( 'ess_owner_website', 			self::url( @$_REQUEST['ess_owner_website'] ) );
			ESS_Database::set_option( 'ess_owner_phone', 			@$_REQUEST['ess_owner_phone'] 			);

		// -- Social Platforms
		foreach ( ESS_Database::$SOCIAL_PLATFORMS as $type => $socials_ )
		{
			foreach ( $socials_ as $social )
			{
				$url = $_REQUEST[ 'ess_social_'.$social ];

				if ( strlen( $url ) > 10 )
				{
					if ( FeedValidator::isValidURL( $url ) )
						ESS_Database::set_option( 'ess_social_'.$social, $url );
					else
						$ESS_Notices->add_error( sprintf( __(
							"The URL you have submited for <b>%s</b> is not valide: <a href='%s' target='_blank'>%s</a>", 'dbem' ),
							$social, $url, $url
						) );
				}
			}
		}

		if ( strlen( $ESS_Notices->get_errors() ) <= 0 )
			$ESS_Notices->add_info( __( "The export setting page have been save correctly.", 'dbem' ) );
	}


	public static function get_categories_types()
	{
		$cat_ = EssDTD::getFeedDTD();

		return ( @count( $cat_ ) > 0 )? $cat_['categories']['types'] : NULL;
	}

	public static function is_form_import_ess_visible()
	{
		global $ESS_Notices;
		if ( @count( $ESS_Notices->get_errors() ) > 0 )
			return FALSE;

		return ( (
			strlen( $_REQUEST['ess_feed_url'] ) > 0 &&
			$_REQUEST['ess_feed_url'] != ESS_IO::HTTP
		)? TRUE : FALSE );
	}

	private static function url( $url )
	{
		return preg_replace( '/[^A-Za-z0-9\.\_\-\:\/\&\?\%\=\*\#\;\(\)\]\[\}\{]/', '', urldecode( $url ) );
	}
}