<?php
/**
  * Controller ESS_Import
  * Control the import of ESS feed
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */
final class ESS_Import
{
	function __construct(){}


	public static function save( $feed_url, $feed_update_daily='on' )
	{
		$feed_url = strtr( str_replace( '&push=1', '', str_replace( '&download=1', '', urldecode( esc_html( $feed_url ) ) ) ), array(
		    "&lt;"   => "<",
		    "&gt;"   => ">",
		    "&quot;" => '""',
		    "&apos;" => "'",
		    "&amp;"  => "&"
		) );

		if ( !is_user_logged_in() && $feed_update_daily != 'on' ) return;

		$feed_update_daily = ( ( !isset( $feed_update_daily ) )? 'off' : 'on' );

		global $ESS_Notices;

		if ( ESS_Import::is_feed_valid( $feed_url ) )
		{
			$RESULT_ = ESS_Import::get_feed_content( $feed_url );

			//dd( $feed_url, $RESULT_ );

			if ( strlen( @$RESULT_['error'] ) <= 0 || @$RESULT_['error'] == NULL )
			{
				//dd( $RESULT_ );

				if ( @count( @$RESULT_['feeds'] ) > 0 && $RESULT_['feeds'] != NULL )
				{
					//if ( trim($feed_url) != trim($RESULT_['link']) )
					//{
					//	$ESS_Notices->add_info(
					//		__("The URL of the feed defined within the feed is not the same then the one you have aggregate.",'dbem') .
					//		"<br/>".
					//		"<a href='".$RESULT_['link']."' target='_blank'>".$RESULT_['link']."</a>" .
					//		"<br/>".
					//		__("and",'dbem').
					//		"<br/>".
					//		"<a href='".$feed_url."' target='_blank'>".$feed_url."</a>"
					//	);
						// Trust more the feed URL specified within the feed or the one entered by the user in the aggregating form (?)
						//$feed_url = $RESULT_['link'];
					//}

					$feed 		 = ESS_Database::get( array( 'feed_uuid' => $RESULT_['id'] ) );
					$feed_id 	 = ( ( @count( $feed ) > 0 )? reset( $feed )->feed_id : 0 );
					$stored_ids_ = ( ( @count( $feed ) > 0 )? explode( ',', reset( $feed )->feed_event_ids ) : array() );

					$post_ids_ 			= array();
					$real_event_ids_ 	= array();
					$existing_events_ 	= array();

					// -- Control if the event is not already in the DB (to update it)
					if ( @count( $stored_ids_ ) > 0 )
					{
						foreach ( $stored_ids_ as $event_id )
						{
							if ( $event_id > 0 )
							{
								$EM_Event_evaluate = em_get_event( $event_id );

								if ( isset( $EM_Event_evaluate->event_name ) )
								{
									array_push( $existing_events_, array(
										'event_title'	=> $EM_Event_evaluate->event_name,
										'event_id'		=> $EM_Event_evaluate->event_id,
										'post_id'		=> $EM_Event_evaluate->post_id
									) );
								}
							}
						}
					}

					//dd( $existing_events_, $RESULT_['feeds'] );

					foreach ( $RESULT_['feeds'] as $i_feed => $FEED_ )
					{
						$event_id = 0;

						foreach ( $existing_events_ as $event_ )
						{
							if ( htmlspecialchars_decode( $event_[ 'event_title' ] ) == htmlspecialchars_decode( $FEED_[ 'generals' ][ 'title' ] ) )
								$event_id = $event_[ 'event_id' ];
						}

						$EM_Event = ESS_Import::create_event_from_feed( $FEED_, $event_id );

						//dd( $EM_Event );

						if ( $EM_Event->event_id > 0 )
						{
							array_push( $real_event_ids_, $EM_Event->event_id );
							array_push( $post_ids_,  	  $EM_Event->post_id  );
						}
					}

					/*
					mail( 'brice@peach.fr', 'POST: '. @count( $_POST ).' file: ' .__FILE__.':'.__LINE__ ,
						'Feed URL: '. 			$feed_url							. '<br/><br/>' .
						'RESULT_' .				@htmlvardump( $RESULT_ ) 			. '<br/><br/>' .
						'global var _POST: ' . 	@htmlvardump( $_POST 			 ) 	. '<br/><br/>' .
						'EM_Event: ' . 			@htmlvardump( $EM_Event 		 ) 	. '<br/><br/>' .
						'post_ids_ : '. 		@htmlvardump( $post_ids_ 		 ),

						'MIME-Version: 1.0' . "\r\n" .'Content-type: text/html; charset=iso-8859-1' . "\r\n"
					);
					*/

					//dd( $real_event_ids_ );

					// == SAVE FEED ===
					// Only save the ESS Feed if, at least, one event have been saved.
					if ( @count( $real_event_ids_ ) > 0 )
					{
						$dom_ = parse_url( $feed_url );

						if ( ESS_Database::add( array(
							'feed_id'			=> $feed_id,
							'feed_uuid'			=> $RESULT_['id'],
							'feed_owner'		=> intval( $EM_Event->owner ),
							'feed_event_ids' 	=> implode(',',$real_event_ids_ ),
							'feed_post_ids' 	=> implode(',',$post_ids_ ),
							'feed_title'		=> $RESULT_['title'],
							'feed_host'			=> $dom_[ 'host' ],
							'feed_url'			=> $feed_url,
							'feed_status'		=> ESS_Database::FEED_STATUS_ACTIVE,
							'feed_mode'			=> ( ( $feed_update_daily == 'on' )? ESS_Database::FEED_MODE_CRON : ESS_Database::FEED_MODE_STANDALONE )
						) ) === TRUE )
						{
							// -- OK: Feed crawled and inserted.
							$ESS_Notices->add_confirm( sprintf( __( "The ESS feed have been %s.", 'dbem' ), __( ( intval( $feed_id ) > 0 )? "updated" : "created", 'dbem' ) ) );
						}
						else $ESS_Notices->add_error( __( "Impossible to insert the ESS Feed in the Data Base: ", 'dbem' ) . ESS_Elements::get_ahref( $feed_url ) );
					}
					else $ESS_Notices->add_error( __( "No events found in the ESS Feed:", 'dbem' ). ESS_Elements::get_ahref( $feed_url ) );
				}
				else $ESS_Notices->add_error( __( "Impossible to analyse the ESS Feed: ",'dbem' ). ESS_Elements::get_ahref( $feed_url ) );
			}
			else $ESS_Notices->add_error( $RESULT_['error'] . "<br/>" . ESS_Elements::get_ahref( $feed_url ) );
		}
	}


	public static function create_event_from_feed( Array $FEED_=NULL, $event_id=NULL )
	{
		global $ESS_Notices, $current_site;

		$EM_Event = NULL;

		if ( $FEED_ != NULL )
		{
			//dd( $FEED_ );

			$EM_Event = new EM_Event( ( intval( $event_id ) > 0 )? $event_id : 0 ); // set eventID for update

			// -- Populate $_POST global var for EM functions
			if ( ESS_Import::set_post_from_feed( $FEED_ ) )
			{
				//dd($_POST);

				if ( $EM_Event->can_manage( 'edit_events', 'edit_recurring_events', 'edit_others_events' ) && $EM_Event->get_post() ) // user must have permissions.
				{
					// -- temporarily remove the save listener to prevent multi-pushing to search engines
					ESS_IO::set_save_filter( FALSE );

					$EM_Location 	= NULL;
					$EM_Categories 	= NULL;
					$EM_Tickets 	= NULL;

					$blog_id = ( ( isset( $current_site ) )? $current_site->blog_id : NULL ); // used as global by some functions (Cf: EM_location::save())

					if ( empty( $event_id ) )
					{
						$EM_Event->force_status 	= ( ( intval( get_option( 'ess_syndication_status' ) ) >= 1 )?
														ESS_Database::EVENT_STATUS_PUBLISH
														:
														ESS_Database::EVENT_STATUS_DRAFT
													);
						$EM_Event->event_status 	= 1;
						$EM_Event->previous_status	= 1;
					}
					else
					{
						$EM_Event->event_id = $event_id;

						// -- Remove old images in case of event's update
						if ( get_option('ess_feed_import_images' ) && intval( $EM_Event->post_id ) > 0 )
							ESS_Images::delete( $EM_Event->post_id );
					}

					$EM_Event->post_status = ( ( strtolower( $_POST[ 'event_access' ] ) == 'private' )? 'private' : ESS_Database::EVENT_STATUS_PUBLISH );



					// == GENERAL
					if ( strlen( $_POST[ 'content' ] ) > 0 )
					{
						if ( get_option( 'ess_backlink_enabled' ) )
						{
							$feed_uri 		= $FEED_[ 'generals' ][ 'uri' ];
							$feed_uri_host 	= parse_url ( $feed_uri, PHP_URL_HOST );

							$_POST['content'] .= "<h6>". __( "Source:", 'dbem') . " <a title=\"". __( "Source:", 'dbem') . " ".$feed_uri_host."\" href=\"" . $feed_uri . "\">" .  parse_url ( $feed_uri, PHP_URL_HOST ) . "</a></h6>";
						}
					}




					// == PLACE / LOCATION
					//dd( $_POST['location_name'] );
					if ( $_POST[ 'no_location' ] === FALSE && strlen( $_POST['location_name'] ) > 0 && get_option( 'dbem_locations_enabled' ) )
					{
						$EM_Location = new EM_Location();

						if ( $EM_Location->can_manage('publish_locations') && $EM_Location->get_post(FALSE) )
						{
							//d( $EM_Location );
							// -- Search if this location already exists in the database
							$similar_ = ESS_Import::load_similar_location( array(
								'location_name'		=> $EM_Location->location_name,
								'location_address'	=> $EM_Location->location_address,
								'location_town'		=> $EM_Location->location_town,
								'location_state'	=> $EM_Location->location_state,
								'location_postcode'	=> $EM_Location->location_postcode,
								'location_country' 	=> $EM_Location->location_country
							) );
							//dd( $similar_ );

							// if the location already exists use it instead.
							if ( @count( $similar_ ) > 0 && $similar_ != NULL )
							{
								foreach ( $similar_ as $key => $val )
									$EM_Location->$key = $val;
							}
							else
							{
								$EM_Location->post_status 	  	= 'publish';
								$EM_Location->location_status 	= 1;
								$EM_Location->post_content 		= '';
							}

							// -- Search & defines latitude / longitude if not set
							if ( FeedValidator::isValidLatitude(  (String)$_POST['location_latitude']  ) == FALSE ||
								 FeedValidator::isValidLongitude( (String)$_POST['location_longitude'] ) == FALSE )
							{
								require_once( EM_ESS_DIR . "/inc/libs/geocoder/GoogleGeocode.php" );
								$geocode_ = GoogleGeocode::getGeocodeFromAddress(
									trim(
										$EM_Location->location_address	. " ".
										$EM_Location->location_town 	. " ".
										$EM_Location->location_postcode . " ".
										$EM_Location->location_country
									)
								);

								$lat = (String)$geocode_['results'][0]['geometry']['location']['lat'];
             					$lng = (String)$geocode_['results'][0]['geometry']['location']['lng'];

								//echo "latitude: " .  $lat . " ==> ".((FeedValidator::isValidLatitude(  $lat ))?'TRUE':'FALSE')."<br/>";
								//echo "longitude: " . $lng . " ==> ".((FeedValidator::isValidLongitude( $lng ))?'TRUE':'FALSE')."<br/>";

								if ( FeedValidator::isValidLatitude(  $lat ) &&
									 FeedValidator::isValidLongitude( $lng ) )
								{
									$EM_Location->location_latitude  = $lat;
									$EM_Location->location_longitude = $lng;
								}
							}

							if ( $EM_Location->save() === FALSE )
								$ESS_Notices->add_error( $EM_Location->get_errors() );

							$EM_Event->location_id = $EM_Location->location_id;
						}
						else $ESS_Notices->add_error( $EM_Location->get_errors() );
					} // end add location
					//dd( $EM_Location );





					// == PRICE / TICKETS
					//dd( $_POST['em_tickets'] );
					if ( @count( $_POST['em_tickets'] ) > 0 && get_option('dbem_rsvp_enabled') )
					{
						$EM_Tickets = new EM_Tickets( $EM_Event );
						$ticket_data = NULL;

						// Create tickets only if they doesn't exists
						if ( @count( $EM_Tickets->tickets ) <= 0 )
						{
							foreach( $_POST['em_tickets'] as $ticket_data )
							{
								$EM_Ticket = new EM_Ticket();
								$EM_Ticket->get_post( $ticket_data );
								$EM_Tickets->tickets[] = $EM_Ticket;
							}
						}

						if ( $ticket_data != NULL )
						{
							$EM_Event->event_rsvp 		= TRUE;
							$EM_Event->event_rsvp_date 	= $ticket_data[ 'event_rsvp_date'	];
							$EM_Event->event_rsvp_time 	= $ticket_data[ 'event_rsvp_time'	];
							$EM_Event->event_spaces 	= $ticket_data[ 'event_spaces'		];
							$EM_Event->rsvp_time		= $ticket_data[ 'event_rsvp_time'	];
						}
					} // end add tickets
					//dd( $EM_Tickets );





					// == CATEGORIES
					//dd( $_POST['event_categories'] );
					if ( @count( $_POST['event_categories'] ) > 0 && get_option( 'dbem_categories_enabled' ) )
					{
						$EM_Categories = new EM_Categories();

						if ( $EM_Categories->can_manage( 'edit_event_categories' ) )
						{
							$caregory_ids_ = array();

							foreach( $_POST['event_categories'] as $category_name )
							{
								$category_term = get_term_by( 'name', $category_name, EM_TAXONOMY_CATEGORY );

								// DEBUG: 2014-01-30
								// Fix a internationalization bug report: http://wordpress.org/support/topic/finding-event-category-by-slug-is-not-always-a-good-idea
								//$category_slug = sanitize_title_with_dashes( $category_name );
								//$category_term = get_term_by( 'slug', $category_slug, EM_TAXONOMY_CATEGORY );

								if ( $category_term === FALSE )
								{
									// Term (with category taxonomy) not created yet, let's create it
									//$term_array = wp_insert_term( $category_name, EM_TAXONOMY_CATEGORY, array( 'slug' => $category_slug ) );
									$term_array = wp_insert_term( $category_name, EM_TAXONOMY_CATEGORY, array( 'name' => $category_name ) );

									if ( intval( $term_array['term_id'] ) > 0 )
										array_push( $caregory_ids_, intval( $term_array['term_id'] ) );
								}
								else
								{
									if ( intval( $category_term->term_id ) > 0 )
										array_push( $caregory_ids_, intval( $category_term->term_id ) );
								}
							}

							$_POST['event_categories'] = $caregory_ids_;

							if ( $EM_Categories->get_post() === FALSE )
								$ESS_Notices->add_error( $EM_Categories->get_errors() );
						}
						else $ESS_Notices->add_error( $EM_Categories->get_errors() );
					} // end add categories
					$EM_Event->categories = $EM_Categories;
					//dd( $EM_Categories );





					// == TAGS
					//dd( $_POST['event_tags'] );
					if ( @count( $_POST[ 'event_tags' ] ) > 0 && get_option( 'dbem_tags_enabled' ) )
					{
						$EM_Tags = new EM_Tags();
						$tag_ids_ = array();

						foreach( $_POST['event_tags'] as $tag_name )
						{
							$tag_slug = sanitize_title_with_dashes( $tag_name );
							$tag_term = get_term_by( 'slug', $tag_slug, EM_TAXONOMY_TAG );

							if ( $tag_term === FALSE )
							{
								// -- Term (with tag taxonomy) not created yet, let's create it
								$term_array = wp_insert_term( $tag_name, EM_TAXONOMY_TAG, array(
									'slug' => $tag_slug
								));

								if ( intval( $term_array['term_id'] ) > 0 )
									array_push( $tag_ids_, intval( $term_array['term_id'] ) );
							}
							else
							{
								if ( intval( $tag_term->term_id ) > 0 )
									array_push( $tag_ids_, intval( $tag_term->term_id ) );
							}
						}

						$_POST['event_tags'] = $tag_ids_;

						if ( $EM_Tags->get_post() === FALSE )
							$ESS_Notices->add_error( $EM_Categories->get_errors() );
					} // end add tags
					//dd( $EM_Tags );







					// == DATE (force date values)
					if ( strlen( $_POST[ 'event_start_date' ] ) > 0 )
					{
						$EM_Event->event_start_date	= $_POST[ 'event_start_date' ];
						$EM_Event->start_date		= $_POST[ 'event_start_date' ];
						$EM_Event->event_start_time	= $_POST[ 'event_start_time' ];
						$EM_Event->start_time		= $_POST[ 'event_start_time' ];

						$EM_Event->event_end_date	= $_POST[ 'event_end_date' ];
						$EM_Event->end_date			= $_POST[ 'event_end_date' ];
						$EM_Event->event_end_time	= $_POST[ 'event_end_time' ];
						$EM_Event->end_time			= $_POST[ 'event_end_time' ];

						$EM_Event->start = strtotime( $EM_Event->event_start_date ." ". $EM_Event->event_start_time );
						$EM_Event->end 	 = strtotime( $EM_Event->event_end_date	  ." ". $EM_Event->event_end_time   );

						if ( $EM_Event->end < date( 'U' ) )
							$ESS_Notices->add_info( __( "An event imported is already finished: ". ESS_Elements::get_ahref( $FEED_[ 'generals' ]['uri'] ), 'dbem' ) );

					} // end add date




					// == PEOPLE
					$EM_Event->post_excerpt = ( ( strlen( $_POST[ 'event_excerpt' ] ) > 0 )? $_POST[ 'event_excerpt' ] : '' );


					// -- update the information already set line 187
					//dd( get_option( 'ess_syndication_status' ) );
					$EM_Event->force_status = ( ( intval( get_option( 'ess_syndication_status' ) ) >= 1 )?
						ESS_Database::EVENT_STATUS_PUBLISH
						:
						ESS_Database::EVENT_STATUS_DRAFT
					);


					//dd( $EM_Event );



					// == SAVE EVENT ======
					$res = $EM_Event->save();

					//dd( $res ); // return FALSE if two of some functions are not updated in EM_Events()

					//echo "event post id: ". $EM_Event->post_id ."<br/>";
					//echo "event event id: ". $EM_Event->event_id."<br/>";

					if ( intval( $EM_Event->post_id ) > 0 )
					{
						// == MEDIA ==========
						if ( @count( $_POST['event_media'] ) )
						{
							//var_dump( $_POST['event_media'] );
							$media_attachement_ = array();
							foreach ( $_POST['event_media'] as $media_ )
							{
								if ( ESS_IO::is_file_exists( $media_[ 'uri' ] ) )
								{
									// Use the same 'manage' value to control the importation of 'images', 'sounds' or 'videos'.
									if ( $EM_Event->can_manage( 'upload_event_images' ) )
									{
										// == IMAGES
										if ( FeedValidator::getMediaType( $media_[ 'uri' ] ) == 'image' && get_option('ess_feed_import_images' ) )
										{
											$attachment_id = ESS_Images::add( array(
												'uri' 		=> $media_[ 'uri' ],
												'name'		=> $media_[ 'name' ],
												'post_id'	=> $EM_Event->post_id
											) );

											if ( $attachment_id !== FALSE && intval( $attachment_id ) > 0 )
											{
												array_push( $media_attachement_, array(
													'attachement_id'	=> $attachment_id,
													'uri'				=> $media_[ 'uri' ]
												) );
											}
											else
												$ESS_Notices->add_error( __( "Impossible to upload the event's image: ", 'dbem' ).ESS_Elements::get_ahref( $media_[ 'uri' ] ) );
										} // end add images

										// == VIDEOS (TODO...)
										if ( FeedValidator::getMediaType( $media_[ 'uri' ] ) == 'video' && get_option('ess_feed_import_videos' ) )
										{
											if ( ESS_Videos::add( array(
												'uri' 		=> $media_[ 'uri' ],
												'name'		=> $media_[ 'name' ],
												'post_id'	=> $EM_Event->post_id
											) ) === FALSE )
												$ESS_Notices->add_error( __( "Impossible to upload the event's video: ", 'dbem' ).ESS_Elements::get_ahref( $media_[ 'uri' ] ) );
										} // end add videos

										// == SOUNDS (TODO...)
										if ( FeedValidator::getMediaType( $media_[ 'uri' ] ) == 'sound' && get_option('ess_feed_import_sounds' ) )
										{
											if ( ESS_Sounds::add( array(
												'uri' 		=> $media_[ 'uri' ],
												'name'		=> $media_[ 'name' ],
												'post_id'	=> $EM_Event->post_id
											) ) === FALSE )
												$ESS_Notices->add_error( __( "Impossible to upload the event's audio file: ", 'dbem' ).ESS_Elements::get_ahref( $media_[ 'uri' ] ) );
										} // end add sounds
									}
									else $ESS_Notices->add_error( $EM_Event->get_errors() );
								}
								else $ESS_Notices->add_info( sprintf( __( "A media file defined in the ESS feed is not reachable: <a href='%s' target='_blank'>%s</a>", 'dbem' ), $media_[ 'uri' ], $media_[ 'uri' ] ) );
							}

							// -- Define image with the highest 'priority' as first attachement
							$priority_test = 1;
							foreach ( $_POST['event_media'] as $media_ )
							{
								if ( get_option( 'ess_feed_import_images' ) == FALSE )
									break;

								if ( @$media_['priority'] == $priority_test )
								{
									if ( FeedValidator::getMediaType( $media_[ 'uri' ] ) == 'image' )
									{
										foreach( $media_attachement_ as $ma_ )
										{
											if ( $ma_['uri'] == $media_[ 'uri' ] && $ma_['attachement_id'] > 0 )
											{
												ESS_Images::delete( $EM_Event->post_id, $ma_[ 'attachement_id' ] );

												$err = ESS_Images::add( array(
													'uri' 		=> $media_[ 'uri' ],
													'name'		=> $media_[ 'name' ],
													'post_id'	=> $EM_Event->post_id
												) );
											}

										}
									}
									else
										$priority_test++;
								}
							}

							// -- Display all the media files thumbnail at the bottom of the event's description
							$imgs_ = ESS_Images::get_thumbnails( $EM_Event->post_id );
							//var_dump( $imgs_ );die;
							if ( @count( $imgs_ ) > 0 )
							{
								$EM_Event->post_content .= "<br/><hr/>";
								foreach ( $imgs_ as $img_ )
								{
									if ( FeedValidator::isValidURL( $img_['url'] ) )
										$EM_Event->post_content .= "<img src='".$img_['url']."' width='".$img_['width']."' height='".$img_['height']."' style='display:inline;margin:5px;'/>";
								}

								// -- UPDATE event's description with thumbnail images at the bottom
								$res = $EM_Event->save();
							}

						} // end add media



						// == TICKETS ==========
						if ( @count( $EM_Tickets->tickets ) > 0 && get_option( 'dbem_rsvp_enabled' ) )
						{
							$EM_Tickets->blog_id  = $blog_id;
							$EM_Tickets->event_id = $EM_Event->event_id;

							if ( $EM_Tickets->save() === FALSE )
								$ESS_Notices->add_error( $EM_Tickets->get_errors() );
						} // end assign event to categories



						// == CATEGORIES ==========
						if ( @count( $EM_Categories->categories ) > 0 && get_option( 'dbem_categories_enabled' ) )
						{
							$EM_Categories->blog_id  = $blog_id;
							$EM_Categories->event_id = $EM_Event->event_id;

							if ( $EM_Categories->save() === FALSE )
								$ESS_Notices->add_error( $EM_Categories->get_errors() );
						} // end assign event to categories



						// == TAGS ==========
						if ( @count( $EM_Tags->tags ) > 0 && get_option( 'dbem_tags_enabled' ) )
						{
							//var_dump( $EM_Tags->tags );

							$EM_Tags->blog_id  = $blog_id;
							$EM_Tags->event_id = $EM_Event->event_id;

							// this function doesn't seem to work...
							if ( $EM_Tags->save() === FALSE )
								$ESS_Notices->add_error( $EM_Tags->get_errors() );

							$tags_ = array();
							foreach ( $EM_Tags->tags as $EM_Tag )
							{
								if ( strlen( $EM_Tag->slug ) > 0 )
									array_push( $tags_, $EM_Tag->slug );
							}
							if ( @count( $tags_ ) > 0 )
								wp_set_object_terms( $EM_Event->post_id, $tags_, EM_TAXONOMY_TAG );

						} // end assign event to tags



						// == LOCATION ==========
						ESS_Database::clear_locations();



						//dd( $EM_Event );

						ESS_IO::set_save_filter( TRUE );
					}
				}
				else $ESS_Notices->add_error( $EM_Event->get_errors() );
			}
		}

		return $EM_Event;
	}

	/**
	 * Convert an ESS feed URL into a Array that contains all the events within this feed.
	 *
	 * @param 	String 	feed_url	URL of the feed to parse
	 * @return 	Array	RESULT_		Enumerate at each row each Event Object contains within the feed
	 */
	public static function get_feed_content( $feed_url="" )
	{
		$feed_url = strtr( @urldecode( $feed_url ), array(
		    "&lt;"   => "<",
		    "&gt;"   => ">",
		    "&quot;" => '""',
		    "&apos;" => "'",
		    "&amp;"  => "&"
		) );

		$RESULT_ = array();

		@assert_options( ASSERT_ACTIVE, 	1 );
		@assert_options( ASSERT_BAIL, 		1 );
		@assert_options( ASSERT_QUIET_EVAL, 1 );

		$timeout_sec = 20; // timeout 20 seconds.
		$fp = @fopen( $feed_url, 'r', FALSE, stream_context_create( array( 'http' => array( 'timeout' => $timeout_sec, 'method' => "GET" ) ) ) );
		@set_time_limit( $timeout_sec );

		if ( $fp !== FALSE )
		{
			try
			{
				$ess = @simplexml_load_file( $feed_url, "SimpleXMLElement", LIBXML_NOCDATA );
				//var_dump( $feed_url, $ess );
			}
			catch( ErrorException $e )
			{
				$ess = FALSE;
				$RESULT_['error'] = "XML Error: An error occure while trying to read the ESS file from the URL: ". $feed_url . "(" .$e. ")";
			}
		}
		else
		{
			$ess = FALSE;
			$RESULT_['error'] = "The ESS Feed request timed out with URL: ". $feed_url;
		}

		if ( $ess !== FALSE )
		{
			$RESULT_['feeds'] = array();

			// -- CHANNEL
			foreach ( $ess->channel->children() as $child )
			{
				$channel = strtolower( $child->getName() );

				if ( $channel != 'feed' )
					$RESULT_[ $channel ] = trim( $ess->channel->$channel );
				else
				{
					// -- FEED
					$FEED_ = array();
					foreach ( $child->children() as $feedChild )
					{
						$feed  = strtolower( $feedChild->getName() );
						$value = trim( $child->$feed );

						//echo "feed: $feed = $value<br>\n";
						//var_dump($feedChild);

						// -- ROOT elements
				  		if ( $feed == 'title' 		||
							 $feed == 'id'			||
							 $feed == 'access'		||
							 $feed == 'description'	||
							 $feed == 'published'	||
							 $feed == 'uri'	 		||
							 $feed == 'updated'
						)
						{
							//echo "feed: $feed = $value<br>\n";
							$FEED_[ 'generals' ][ $feed ] = (string)$value;
						}

						if ( $feed == 'tags' )
						{
							if ( ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 )? $child->$feed->count() : @count( $child->$feed->children() ) > 0 )
							{
								$FEED_[ $feed ] = array();

								foreach ( $child->$feed->children() as $tag )
				  					array_push( $FEED_[ $feed ], (string)$tag );
							}
						}

						// -- COMPLEX elements
						if ( $feed == 'categories' 	||
							 $feed == 'dates'		||
							 $feed == 'places'		||
							 $feed == 'prices'		||
							 $feed == 'media'		||
							 $feed == 'people'		||
							 $feed == 'relations'	||
							 $feed == 'authors'
						)
						{
							// -- Check <item>s childs values
							$FEED_[ $feed ] = array();

							foreach ( $child->$feed->item as $complexItem )
							{
								$arr_ = array();
								$attr = $complexItem->attributes();
								switch ( $feed )
								{
									default :
									case 'categories' :
									case 'people' :
									case 'media' :
									case 'relations' :
										if ( strlen($attr->type				) > 0 ) {$arr_[ 'type' ]			= strtolower( $attr->type );}
										if ( strlen($attr->priority			) > 0 ) {$arr_[ 'priority' ]		= intval( $attr->priority );}
										break;

									case 'places' :
										if ( strlen($attr->type				) > 0 ) {$arr_[ 'type' ]			= strtolower( $attr->type );}
										if ( strlen($attr->priority			) > 0 ) {$arr_[ 'priority' ]		= intval( $attr->priority );}
										if ( strlen($attr->moving_position	) > 0 ) {$arr_[ 'moving_position' ]	= intval( $attr->moving_position );}
										break;

									case 'prices' :
										if ( strlen($attr->type				) > 0 ) {$arr_[ 'type' ]			= strtolower( $attr->type );}
										if ( strlen($attr->priority			) > 0 ) {$arr_[ 'priority' ]		= intval( $attr->priority );}
										if ( strlen($attr->mode				) > 0 ) {$arr_[ 'mode' ]			= strtolower( $attr->mode );}
										if ( strlen($attr->unit				) > 0 ) {$arr_[ 'unit' ]			= strtolower( $attr->unit );}
										if ( strlen($attr->limit			) > 0 ) {$arr_[ 'limit' ]			= intval( $attr->limit );}
										if ( strlen($attr->interval			) > 0 ) {$arr_[ 'interval' ]		= intval( $attr->interval );}
										if ( strlen($attr->selected_day		) > 0 ) {$arr_[ 'selected_day' ]	= strtolower( $attr->selected_day );}
										if ( strlen($attr->selected_week	) > 0 ) {$arr_[ 'selected_week' ]	= strtolower( $attr->selected_week );}
										break;

									case 'dates' :
										if ( strlen($attr->type				) > 0 ) {$arr_[ 'type' ]			= strtolower( $attr->type );}
										if ( strlen($attr->priority			) > 0 ) {$arr_[ 'priority' ]		= intval( $attr->priority );}
										if ( strlen($attr->unit				) > 0 ) {$arr_[ 'unit' ]			= strtolower( $attr->unit );}
										if ( strlen($attr->limit			) > 0 ) {$arr_[ 'limit' ]			= intval( $attr->limit );}
										if ( strlen($attr->interval			) > 0 ) {$arr_[ 'interval' ]		= intval( $attr->interval );}
										if ( strlen($attr->selected_day		) > 0 ) {$arr_[ 'selected_day' ]	= strtolower( $attr->selected_day );}
										if ( strlen($attr->selected_week	) > 0 ) {$arr_[ 'selected_week' ]	= strtolower( $attr->selected_week );}
										break;
								}


								foreach ( $complexItem->children() as $complexItemChild )
				  				{
				  					$complexName 	= strtolower( $complexItemChild->getName() );
									$complexValue 	= trim( $complexItemChild );

									$arr_[ $complexName ] = $complexValue;
								}
								array_push( $FEED_[ $feed ], $arr_ );
							}
						}
					}

					//echo "------------------------------------------<br>";
					//dd( $FEED_ );

					if ( count( $FEED_ ) > 0 )
						array_push( $RESULT_['feeds'], $FEED_ );
				}
			}
		}
		return $RESULT_;
	}

	/*
	 *	temporary function
	 *	Rewrite the EM function in /events-manager/classes/em-location.php:536
	 **/
	public static function load_similar_location( Array $DATA_ = NULL )
	{
		$r_ = array();

		global $wpdb;
		$wpdb->show_errors( FALSE );

		if ( !empty( $DATA_[ 'location_name' ] ) )
		{
			$sql =
			" SELECT * FROM ". EM_LOCATIONS_TABLE .
			" WHERE location_name = ". 															$wpdb->prepare( "%s", $DATA_['location_name'] 			   ) .
			( ( strlen( @$DATA_['location_address'] 	) > 0 )? " AND 	location_address = " . 	$wpdb->prepare( "%s", $DATA_['location_address'] 	) : "" ) .
			( ( strlen( @$DATA_['location_town'] 		) > 0 )? " AND 	location_town = " . 	$wpdb->prepare( "%s", $DATA_['location_town'] 		) : "" ) .
			( ( strlen( @$DATA_['location_state'] 		) > 0 )? " AND 	location_state = " . 	$wpdb->prepare( "%s", $DATA_['location_state'] 		) : "" ) .
			( ( strlen( @$DATA_['location_postcode'] 	) > 0 )? " AND 	location_postcode = " . $wpdb->prepare( "%s", $DATA_['location_postcode'] 	) : "" ) .
			( ( strlen( @$DATA_['location_country'] 	) > 0 )? " AND 	location_country = " . 	$wpdb->prepare( "%s", $DATA_['location_country'] 	) : "" ) .
			"";

			return $wpdb->get_row( $sql, ARRAY_A );
		}
		return $r_;
	}


	/**
	 * Convert an Event feed object contains within the feed (previously parsed by ESS_Import::get_feed_content())
	 * into the relevents $_POST[xxx] elements, to be ready to be aggregated by Wordpress.
	 *
	 * @param 	Array	FEED_	Array that contain the relevent elements of an event.
	 * @return 	Boolean	result	return a boolean value.
	 *
	 */
	public static function set_post_from_feed( $FEED_ )
	{
		//dd( $FEED_ );

		$_REQUEST[ 'action'	  ] = 'event_save';
		$_REQUEST[ 'event_id' ]	= 0;

		$_POST[ 'action'	] 	= $_REQUEST[ 'action'   ];
		$_POST[ 'event_id'	] 	= $_REQUEST[ 'event_id' ];

		//dd( $FEED_[ 'generals' ] );

		$event_attr_ = explode( ESS_Feed::CUSTOM_ATTRIBUTE_SEPARATOR, $FEED_[ 'generals' ][ 'description' ] );
		$_POST[ 'event_attributes' ] = ( ( @count( $event_attr_ ) == 3 && $event_attr_ != NULL && strlen( ESS_Feed::CUSTOM_ATTRIBUTE_SEPARATOR ) > 0 )?
			json_decode( @$event_attr_[ 1 ], TRUE )
			:
			array()
		);

		$_POST[ 'em_attributes' ] = $_POST[ 'event_attributes' ];

		$_POST[ 'event_excerpt' ] 	= NULL;
		$_POST[ 'event_slug' ] 		= $FEED_[ 'generals' ][ 'id' ];
		$_POST[ 'event_name' ] 		= $FEED_[ 'generals' ][ 'title' ];
		$_POST[ 'content' ] 	 	= $FEED_[ 'generals' ][ 'description' ];
		$_POST[ 'event_access' ] 	= $FEED_[ 'generals' ][ 'access' ];
		//$FEED_[ 'generals' ][ 'uri' ];
        //$FEED_[ 'generals' ][ 'published' ];

		//dd( $_POST );

        $minpeople 	= 0; // can also be in custom attributes...
        $maxpeople 	= 0;

		// ==== PEOPLE ============================
		if ( @count( @$FEED_[ 'people' ] ) > 0 && @$FEED_[ 'people' ] != NULL )
    	{
    		//dd( $FEED_[ 'people' ] );

			foreach ( $FEED_[ 'people' ] as $people_ )
			{
				if ( $people_['type'] == 'organizer' || $people_['type'] == 'author' )
				{
					$_POST['event_owner_name']	= ( !isset( $_POST['event_owner_name']  ) )? @$people_[ 'name' ]  : $_POST['event_owner_name'];
					$_POST['event_owner_email']	= ( !isset( $_POST['event_owner_email'] ) )? @$people_[ 'email' ] : $_POST['event_owner_email'];

					// === element available ===
					//$people_[ 'firstname' ];
                    //$people_[ 'lastname' ];
                    //$people_[ 'organization' ];
                    //$people_[ 'logo' ];
                    //$people_[ 'icon' ];
                    //$people_[ 'uri' ];
                    //$people_[ 'address' ];
                    //$people_[ 'city' ];
                    //$people_[ 'zip' ];
                    //$people_[ 'state' ];
                    //$people_[ 'state_code' ];
                    //$people_[ 'country' ];
                    //$people_[ 'country_code' ];
                    //$people_[ 'phone' ];
				}

				else if ( $people_[ 'type' ] == 'attendee' )
				{
					$maxpeople 	= @$people_[ 'maxpeople' ];
					$minpeople	= @$people_[ 'minpeople' ];

       				$_POST[ 'event_excerpt' ] = trim( @$people_[ 'restriction' ] .
       					( ( intval( @$people_[ 'minage' ] ) > 0 )?
       						" min age:" . $people_[ 'minage' ]
       						: ''
			   			)
					);
				}

				else if ( $people_[ 'type' ] == 'social' )
				{
				 	//$people_[ 'name' ];
                    //$people_[ 'uri' ];
               	}
			}
		}

		// ==== DATES ============================
        if ( @count( @$FEED_[ 'dates' ] ) > 0 && @$FEED_[ 'dates' ] != NULL )
 		{
	    	$_POST['event_all_day'] = 0;

	        //dd( $FEED_[ 'dates' ] );

	    	$date_ = $FEED_[ 'dates' ][0];  //!\\ TAKE IN CONSIDERATION ONLY THE FIRST FEED DATE

			if ( FeedValidator::isValidDate( $date_[ 'start' ] ) )
			{
				$dur 	= floatval( @$date_[ 'duration' ] );
				$unit 	= strtolower( ( strlen( @$date_[ 'unit' ] ) > 0 )? $date_[ 'unit' ]."s" : ' seconds' );

				if ( $unit == 'hours' && $dur > 0 )
				{
					$dur  = $dur*60;
					$unit = "minutes";
				}

				$myDateTime = new DateTime( @$date_[ 'start' ], new DateTimeZone( 'GMT' ) );
				$date_start = $myDateTime->format( 'Y-m-d H:i:s' );
				$str_start	= $date_start . ( ( $dur > 0 )? " + " . $dur . " " . $unit : "" );

				$_POST['event_start_date']	= date( 'Y-m-d', strtotime( $date_start ) );
				$_POST['event_end_date']	= date( 'Y-m-d', strtotime( $str_start  ) );

				$_POST['event_start_time'] 	= date( 'H:i:s', strtotime( $date_start ) );
				$_POST['event_end_time']	= date( 'H:i:s', strtotime( $str_start  ) );

				//dd( $dur, $str_start, strtotime( $str_start ) );

	    		if ( $date_[ 'type' ] == 'recurrent' )
				{
					$sd = explode(',', @$date_[ 'selected_day' ] ); 	// 'monday','tuesday','wednesday','thursday','friday','saturday','sunday' OR 1,2,3,4,5,6,7,8,9,10,11,12,1,3,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
					$sw = explode(',', @$date_[ 'selected_week' ] );	// 'first', 'second', 'third', 'fourth', 'last'
					$u  = @$date_[ 'unit' ];  							// 'hour','day','week','month','year'
					$l  = @$date_[ 'limit' ];							// integer: number of time the recursivity will occure.
					$delay = "+" . $l . " " . $unit;

					//echo "delay: ". $delay . " => " .date( DateTime::ATOM, strtotime( @$date_[ 'start' ]. " + ".$dur." seconds" ) );

					$_POST['recurring'] 			= TRUE;
					$_POST['event_end_date']		= date( 'Y-m-d', strtotime( $delay, strtotime( $str_start ) ) );
					$_POST['event_end_time' ]		= date( 'H:i:s', strtotime( $delay, strtotime( $str_start ) ) );
					$_POST['recurrence_interval'] 	= @$date_[ 'interval' ];
					$_POST['recurrence_freq'] 		= 	( ( $u == 'day'   )? 'daily'   :
														( ( $u == 'week'  )? 'weekly'  :
														( ( $u == 'month' )? 'monthly' :
														( ( $u == 'year'  )? 'yearly'  :
																			 'hourly'
														) ) ) );

					$_POST[ 'recurrence_byweekno' ] 	= "";
					if ( strlen( @$date_[ 'selected_week' ] ) > 0 && @count( $sw ) > 0 )
					{
						$_POST['recurrence_byweekno'] = array();
						foreach ( $sw as $i => $sw_v )
						{
							array_push(
								$_POST['recurrence_byweekno'],
								( ( $sw_v == 'first'  )? 1 :
								( ( $sw_v == 'second' )? 2 :
								( ( $sw_v == 'third'  )? 3 :
								( ( $sw_v == 'fourth' )? 4 :
											 /*last*/	-1
								))))
							);
						}
						$_POST['recurrence_byweekno'] = implode(',',$_POST['recurrence_byweekno']);
					}
					else
					{
						// if no week number have been specified in the feed attribute "selected_week", get the week number of the event as the default recurent value.
						if ( $u == 'month' && isset( $date_[ 'start' ] ) && !isset( $date_[ 'selected_day' ] ) )
						{
							$ed = $_POST['event_end_date'];
							$weekno = FeedValidator::getDateDiff( 'ww', $ed, date( DateTime::ATOM, strtotime( '01/'.date('m',$ed).'/'.date('Y',$ed) ) ) )+1;
							$_POST['recurrence_byweekno'] = array( ( $weekno > 4 )? -1 : $weekno );
						}
					}
					$_POST['recurrence_byday'] = $_POST['recurrence_bydays'];

					$_POST['recurrence_days']	= "";
					$_POST['recurrence_bydays'] = "";
					$_POST['recurrence_byday']	= "";
					if ( strlen( @$date_[ 'selected_day' ] ) > 0 && @count( $sd ) > 0 )
					{
						$_POST['recurrence_days']	= array();
						$_POST['recurrence_bydays'] = array();

						foreach ( $sd as $i => $sd_v )
						{
							if ( intval( $sd_v ) > 1 )
							{
								array_push(
									$_POST['recurrence_days'],
									$sd_v
								);
							}
							else
							{
								array_push(
									$_POST['recurrence_bydays'],
									( ( $sd_v == 'monday'    )? 1 :
									( ( $sd_v == 'tuesday'   )? 2 :
									( ( $sd_v == 'wednesday' )? 3 :
									( ( $sd_v == 'thursday'  )? 4 :
									( ( $sd_v == 'friday'    )? 5 :
									( ( $sd_v == 'saturday'  )? 6 :
												 /*sunday*/		0
									))))))
								);
							}
						}
						$_POST['recurrence_bydays'] = implode(',',$_POST['recurrence_bydays']);
						$_POST['recurrence_days'] 	= implode(',',$_POST['recurrence_days']);
					}
					else
					{
						// if no weekday have been specified in the feed attribute "selected_day", get the weekday of the event as the default recurrent day.
						if ( $u == 'week' && isset( $date_[ 'start' ] ) )
						{
							$weekday = date( 'N', @$date_[ 'start' ] );
							$_POST['recurrence_bydays'] = array( ( $weekday == 7 )? 0 : $weekday );
						}
					}
					$_POST['recurrence_byday'] = $_POST['recurrence_bydays'];
				}
			}
			//dd($_POST);
		}

		// ==== PRICES ===========================
        if ( @count( @$FEED_[ 'prices' ] ) > 0 && @$FEED_[ 'prices' ] != NULL )
        {
        	//dd( $FEED_[ 'prices' ] );

			$_POST['em_tickets'] = array();
			foreach ( $FEED_[ 'prices' ] as $price_ )
			{
				$date_start = '';
				$date_end 	= '';

				$dur = floatval( @$price_[ 'duration' ] );

				$price_description = trim( $price_[ 'value' ] . ' ' . @$price_[ 'currency' ] . ' ' . @$price_[ 'uri' ] );

				if ( strlen( @$price_[ 'start' ] ) > 0 && $dur > 0 )
				{
					$date_start = date( 'Y-m-d H:i:s', strtotime( @$price_[ 'start' ] ) );
					$date_end 	= date( 'Y-m-d H:i:s', strtotime( @$price_[ 'start' ] . " + ".$dur." seconds" ) );

					//echo "Date Range: ". $date_start ." => " .$date_end;

					if ( $price_[ 'type' ] == 'recurrent' )
					{
						$u = @$price_[ 'unit' ];  	// 'hour','day','week','month','year'
						$l = @$price_[ 'limit' ];	// integer: number of time the recurcivity will occure.

						$price_description .= trim( "<br/>Reccurent billing for " . $l. " ".$u );

						$delay = "+".$l." ".$u.(($l>1)?'s':'');

						//echo "delay: ". $delay . " => " .date( DateTime::ATOM, strtotime( @$price_[ 'start' ]. " + ".$dur." seconds" ) );

						// Defines the ticket's end date at the end of the recurcivity
						$date_end = date( 'Y-m-d H:i:s', strtotime( $delay, strtotime( $date_start ." + ". $dur." seconds" ) ) );
					}
				}

				array_push( $_POST['em_tickets'], array(
					'event_rsvp_date'		=> ((!empty($date_start))?date('Y-m-d', strtotime( $date_start ) ):''),
					'event_rsvp_time'		=> ((!empty($date_start))?date('H:i:s', strtotime( $date_start ) ):''),
					'ticket_id' 			=> 0,
					'ticket_name' 			=> $price_[ 'name' ],
					'ticket_description' 	=> $price_description,
					'booking_comment'		=> $price_description,
					'ticket_price' 			=> $price_[ 'value' ],
					'ticket_start' 			=> $date_start,
					'ticket_end' 			=> $date_end,
					'ticket_min' 			=> $minpeople,
					'ticket_max' 			=> $maxpeople,
					'ticket_spaces' 		=> $maxpeople, // 10
					'event_spaces'			=> $maxpeople, // 10
					'ticket_members' 		=> FALSE,
					'ticket_guests' 		=> (($price_[ 'mode' ] == 'invitation' 	)? TRUE : FALSE ),
					'ticket_required' 		=> (($price_[ 'mode' ] != 'free' 		)? TRUE : FALSE ),
					'event_rsvp'			=> TRUE
				));
				//$_POST['booking_tax_rate'] = '';

				// -- available values:
				//$price[ 'type' ];
	           	//$price[ 'mode' ];
	            //$price[ 'unit' ];
	            //$price[ 'interval' ];
	            //$price[ 'selected_day' ];
				//$price[ 'selected_week' ];
				//$price[ 'limit' ];
	            //$price[ 'value' ];
	            //$price[ 'name' ];
	            //$price[ 'start' ];
	            //$price[ 'duration' ];
	            //$price[ 'currency' ];
	            //$price[ 'uri' ];
			}
		}

		// ==== PLACES ===========================
        if ( @count( @$FEED_[ 'places' ] ) > 0 && @$FEED_[ 'places' ] != NULL )
        {
        	//dd( $FEED_[ 'places' ] );

			$place_ = $FEED_[ 'places' ][0]; //!\\ TAKE IN CONSIDERATION ONLY THE FIRST FEED'S PLACE

			if ( empty( $place_['name'] ) )
			{
				$_POST['no_location'] = TRUE;
			}
			else
			{
				$_POST['no_location'] 		= FALSE;
				$_POST['location_name'] 	= $place_[ 'name' ];
				$_POST['location_address'] 	= ( ( strlen( @$place_[ 'address' ] ) > 0 )? $place_[ 'address' ] : $place_[ 'name' ] );
				$_POST['location_town']		= @$place_[ 'city'			];
				$_POST['location_state']	= @$place_[ 'state_code'	];
				$_POST['location_postcode']	= @$place_[ 'zip'			];
				$_POST['location_region']	= @$place_[ 'state'			];
				$_POST['location_country']	= @$place_[ 'country_code'	];
				$_POST['location_latitude']	= @$place_[ 'latitude'		];
				$_POST['location_longitude']= @$place_[ 'longitude'		];
			}
		}
		else
		{
			$_POST['no_location'] = TRUE;
		}

		// ==== TAGS =============================
        if ( @count( @$FEED_[ 'tags' ] ) > 0 && @$FEED_[ 'tags' ] != NULL )
        {
        	//dd( $FEED_[ 'tags' ] );

			$tags_ = array();

			foreach ( $FEED_[ 'tags' ] as $i => $tag )
			{
				if ( strlen( $tag ) > 1 && is_numeric( $tag ) == FALSE )
        			array_push( $tags_, $tag );
			}

			if ( count( $tags_ ) > 0 )
				$_POST['event_tags'] = $tags_;
		}

		// ==== CATEGORIES ==========================
		if ( @count( @$FEED_[ 'categories' ] ) > 0 && @$FEED_[ 'categories' ] != NULL )
        {
        	//dd( $FEED_[ 'categories' ] );

			$_POST['event_categories'] = array();

            foreach ( $FEED_[ 'categories' ] as $i => $cat )
       	 		array_push( $_POST['event_categories'], $cat[ 'name' ] );
		}

		// ==== MEDIA ============================
        if ( @count( @$FEED_[ 'media' ] ) > 0 && @$FEED_[ 'media' ] != NULL )
        {
        	//dd( $FEED_[ 'media' ] );

			$_POST[ 'event_media' ] = array();
			foreach ( $FEED_[ 'media' ] as $i => $media_ )
            {
            	//dd( $media_ );

				if ( FeedValidator::isValidURL( $media_[ 'uri' ] ) )
				{
					array_push(
						$_POST[ 'event_media' ],
						array(
							'uri' 		=> $media_[ 'uri' ],
							'name' 		=> $media_[ 'name' ],
							'type'		=> FeedValidator::getMediaType( $media_[ 'uri' ] ), // 'image', 'video', 'sound'
							'priority'	=> $media_[ 'priority' ]
						)
					);
				}
			}
		}

		return TRUE;
	}

	/**
	 * Simple test to check if the URL targets to an existing file.
	 *
	 * @param 	String	feed_url	URL of the ess feed file to test
	 * @return	Boolean	result		return a boolean value.
	 */
	public static function is_feed_valid( $feed_url='' )
	{
		global $ESS_Notices;

		if ( FeedValidator::isValidURL( $feed_url ) == FALSE )
		{
			$ESS_Notices->add_error( sprintf( __( "The ESS URL is not valid: <a href='%s' target='_blank'>%s</a>", 'dbem' ), $feed_url, $feed_url ) );
			return FALSE;
		}
		else
		{
			$response = json_decode( ESS_IO::get_curl_result( FeedWriter::$VALIDATOR_WS, $feed_url ), TRUE );

			//dd( $response );

			if ( $response !== FALSE )
			{
				$r = @$response[ 'result' ];

				//dd( $r );

				if ( @isset( $r[ 'result' ] ) )
				{
					if ( strlen( @$r['result']['error'] ) > 1 || @$r['type'] == 'KO' )
					{
						//d( strlen( @$r['result']['error'] ) );

						if ( $response['result']['error'] == NULL )
							$response['result']['error'] = array();

						array_unshift( 	$response['result']['error'], "<b>" . sprintf( __( "The Feed URL is not a valide ESS file: <a href='%s' target='_blank'>%s</a>", 'dbem' ), $feed_url, $feed_url ) . "</b><br>" );
						array_push( 	$response['result']['error'], "<b>" . sprintf( __( "More information about the standard: <a href='%s' target='_blank'>%s</a>", 'dbem' ), ESS_IO::ESS_WEBSITE, ESS_IO::ESS_WEBSITE ). "</b><br>" );

						//dd( $r );

						$ESS_Notices->add_error( $response );

						return FALSE;
					}
				}
				return TRUE;
			}
		}
		return FALSE;
	}

}