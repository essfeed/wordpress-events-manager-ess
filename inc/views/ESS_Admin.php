<?php
/**
  * View ESS_Admin
  * Container of all the structure of the admin page to manage the ESS settings.
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  */
final class ESS_Admin
{
	function __construct(){}


	public static function main_page()
	{
		global $ESS_Notices;

		ESS_Control_admin::control_requirement();
		ESS_Control_admin::control_import_ESS();
		ESS_Control_admin::control_nav_actions();
		ESS_Control_admin::control_export_forms();

		wp_enqueue_script('jquery');

		?><style  type="text/css" 	 	charset="utf-8"><?php include( EM_ESS_DIR.'/assets/css/admin.css'); ?></style>
		<script type="text/javascript" 	charset="utf-8"><?php include( EM_DIR.'/includes/js/admin-settings.js'); ?></script>
		<script type="text/javascript" 	charset="utf-8"><?php include( EM_ESS_DIR.'/assets/js/jquery.toggles.min.js'); ?></script>
		<script type="text/javascript" 	charset="utf-8"><?php include( EM_ESS_DIR.'/assets/js/jquery.maphilight.js'); ?></script>
		<script type="text/javascript" 	charset="utf-8"><?php include( EM_ESS_DIR.'/assets/js/jquery.timezone-picker.js'); ?></script>
		<script type="text/javascript" 	charset="utf-8"><?php include( EM_ESS_DIR.'/assets/js/admin.js'); ?></script>
		<div id="ess_loader" style="display:none;">
			<p style="background-image:url('<?php echo EM_ESS_URL;?>/assets/img/loader_orange.gif');"></p>
			<div><?php _e( 'please wait...', 'dbem' ); ?></div>
		</div>
		<div class="wrap">
			<div id="icon-options-general" class="icon32" style="background:url('<?php echo EM_ESS_URL;?>/assets/img/ESS_icon_32x32.png') 0 0 no-repeat;"><br/></div>
			<h2 class="nav-tab-wrapper">
				<a href="#export" id="em-menu-export" class="nav-tab nav-tab-active"><?php _e('Settings','dbem'); ?></a>
				<a href="#import" id="em-menu-import" class="nav-tab"><?php _e('Syndication','dbem'); ?></a>
				<a id="btAddESS" class="add-new-h2" title="<?php _e('Import an ESS Feed','dbem'); ?>" style="display:none;">+</a>
				<a id="btViewErrors" class="add-new-h2" title="<?php _e('View Previous Errors','dbem'); ?>" style="display:none;"><?php _e('errors','dbem'); ?></a>
			</h2>
			<form id="em-options-form" method="post"  enctype='multipart/form-data' target="_self" onsubmit="ess_admin.loader();">

				<?php echo $ESS_Notices; ?>

				<?php


					//ESS_Database::update_feeds_daily();
				?>

				<?php self::get_feed_form();?>

				<?php self::export_page(); 	?>
				<?php self::import_page(); 	?>

			</form>

		</div><?php
	}

	private static function get_feed_form()
	{

		global $ESS_Notices;

		?><div id="add_feed_form" class="highlight" style="display:<?php echo ( ( ESS_Control_admin::is_form_import_ess_visible() )?'block;':'none;');?>">
			<?php ESS_Elements::get_explain_block(
				"This section control the ESS feeds aggregated to your websites. (".ESS_Elements::get_ahref(ESS_IO::ESS_WEBSITE).")".
				"<br/>".
				"<b>Import an ESS feed to get the events, coming from another website, presented and updated in your website.</b>"
				//"You can specify if the events aggregated have to be updated daily (to always have the latest information coming from the original websites)."
			);?>
			<div id="titlediv">
				<input type="text" id="title" name="ess_feed_url" autocomplete="off" value="<?php echo (( isset( $_REQUEST['ess_feed_url'] ) && @$ESS_Notices->get_errors()>0 )? $_REQUEST['ess_feed_url']:ESS_IO::HTTP); ?>" onclick="ess_admin.control_ess_import_field(jQuery(this));" />
				<div class="iphone <?php echo ( ( isset( $_REQUEST['ess_feed_mode'] ) )? ( ( $_REQUEST['ess_feed_mode'] == 'on' )? 'on' : 'off' ) : 'off' );?>" id="ess_mode" data-checkbox="ess_feed_mode_checkbox" data-ontext="<?php _e('Update Daily','dbem'); ?>" data-offtext="<?php _e('Import Once','dbem'); ?>"><?php _e('Update Daily','dbem'); ?></div>
				<input type="checkbox" class="ess_feed_mode_checkbox" id="ess_feed_mode" name="ess_feed_mode" <?php echo( ( isset( $_REQUEST['ess_feed_mode'] ) )? ( ( $_REQUEST['ess_feed_mode'] == 'on' )? "checked='checked'" : '' ) : '' );?> />
				<input type="submit" value="<?php _e('ADD','dbem'); ?>" class="button-primary" id="bt_add_feed" />
			</div>
		</div><?php
	}

	private static function get_nav_action()
	{
		$view = strtolower( ( isset( $_REQUEST['view'] ) )? $_REQUEST['view'] : 'all' );

		?><div class="tablenav top">
			<div class="alignleft actions">
				<select name="action">
					<option value="-1" selected="selected"><?php _e('Bulk Actions','dbem'); ?></option>
					<option value="active"><?php echo (( empty( $view ) || $view == 'all' )?__('Move to Active','dbem'):__('Restore','dbem') );?></option>
					<option <?php echo (( $view == 'trash' )?'value="full_deleted">Delete Permanently':'value="deleted">'.__('Move to Trash','dbem') );?></option>
					<?php if ( empty( $view ) || $view == 'all' ) : ?>
					<option value="update_cron"><?php _e('Save Daily Updates','dbem'); ?></option>
					<?php endif; ?>
				</select>
				<input class="button action" type="submit" value="<?php _e('Apply','dbem'); ?>" name="apply_ess_table_filter" />
			</div>
		</div><?php
	}

	// === SYNDICATION TAB ===
	private static function import_page()
	{
		$view 			= strtolower( ( isset( $_GET['view'] ) )? $_GET['view'] : 'all' );
		$active_count 	= ESS_Database::count(array('feed_status'=>ESS_Database::FEED_STATUS_ACTIVE));
		$trash_count  	= ESS_Database::count(array('feed_status'=>ESS_Database::FEED_STATUS_DELETED));
		$efi 		 	= ESS_Database::get(array('feed_status'=>( ( $view == 'trash' )? ESS_Database::FEED_STATUS_DELETED : ESS_Database::FEED_STATUS_ACTIVE )));

		$url_all_events 	= em_add_get_params( $_SERVER['REQUEST_URI'], array('view'=>'all'   ) )."#import";
		$url_trash_events 	= em_add_get_params( $_SERVER['REQUEST_URI'], array('view'=>'trash' ) )."#import";

		if ( $view == 'trash' && $trash_count <= 0 )
		{
			@wp_redirect( $url_all_events );
			exit;
		}
		//d( $efi );

		?><div class="em-menu-import em-menu-group" style="display:none;">

			<!-- PAGES NAV -->
			<div class="subsubsub">
				<a href='<?php echo $url_all_events; ?>' <?php echo ( empty( $view ) || $view == 'all' )? 'class="current"':''; ?>><?php _e ( 'All', 'dbem' ); ?> <span class="count">(<?php echo $active_count; ?>)</span></a>
				<?php if ($trash_count>0):?>&nbsp;|&nbsp;
				<a href='<?php echo $url_trash_events; ?>' <?php echo ( $view == 'trash' )? 'class="current"':''; ?>><?php _e ( 'Trash', 'dbem' ); ?> <span class="count">(<?php echo $trash_count;   ?>)</span></a>
				<?php endif ?>
			</div><?php

			// ACTIONS NAV
			self::get_nav_action();

			$rowno = 0;

			$next_cron  = NULL;

			foreach( get_option( 'cron' ) as $timestamp => $date_ )
			{
				if ( isset( $date_[ ESS_IO::CRON_EVENT_HOOK ] ) )
				{
					$next_cron = $timestamp;
					// d( ESS_Timezone::get_date_GMT( date('U') ), ESS_Timezone::get_date_GMT( $timestamp ) );
				}
			}

			?><!-- LIST -->
			<input type="hidden" value="" id="selected_event_id" name="selected_event_id" />
			<table class='widefat'>
				<thead>
					<tr>
						<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
						<th width="18"></th>
						<th><?php _e('Title', 		'dbem') ?></th>
						<th><?php _e('Nb Events', 	'dbem') ?></th>
						<th><?php _e('Owner', 		'dbem') ?></th>
						<th width="90"><?php _e('Update Daily', 'dbem') ?></th>
						<th width="100"><?php _e('Last Update', 'dbem') ?></th>
						<th width="100"><?php _e('Next Update', 'dbem') ?></th>
						<th width="45"><?php _e('Update', 		'dbem') ?></th>
						<th width="50"><?php _e('View',			'dbem') ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1'/></th>
						<th></th>
						<th><?php _e('Title', 		'dbem') ?></th>
						<th><?php _e('Nb Events', 	'dbem') ?></th>
						<th><?php _e('Owner', 		'dbem') ?></th>
						<th><?php _e('Update', 		'dbem') ?></th>
						<th><?php _e('Last Update', 'dbem') ?></th>
						<th><?php _e('Next Update', 'dbem') ?></th>
						<th><?php _e('Update Daily','dbem') ?></th>
						<th><?php _e('View', 		'dbem') ?></th>
					</tr>
				</tfoot>
				<tbody><?php

					if ( @count( $efi ) > 0 )
					{
						foreach ( $efi as $feed )
						{
							$user = get_userdata( $feed->feed_owner );
							$owner = ((isset($user))?$user->data->display_name:'');
							$event_ids = explode(',', $feed->feed_event_ids );
							$class = ($rowno % 2) ? 'alternate' : '';
							$rowno++;

						?><tr class="<?php echo $class; ?>">
							<th class="check-column" scope="row">
								<label class="screen-reader-text" for="cb-select-<?php echo $feed->feed_id; ?>">Select My first event</label>
								<input type='checkbox' class='row-selector' id="cb-select-<?php echo $feed->feed_id; ?>" value='<?php echo $feed->feed_id; ?>' name='feeds[]'/>
								<div class="locked-indicator"></div>
							</th>
							<td align="center">
					 			<img src="http://www.google.com/s2/favicons?domain=http://<?php echo $feed->feed_host; ?>" width="16" height="16" alt="<?php echo $feed->feed_host; ?>" />
					 		</td>
							<td>
								<strong class="row-title"><?php echo $feed->feed_title; ?></strong>
							</td>
							<td align="left">
								<?php echo @count( $event_ids ); ?>
							</td>
							<td class="author column-author">
								<a><?php echo $owner; ?></a>
							</td>
							<td>
								<?php ESS_Elements::button_checkbox( array(
									'checked'		=> ( ( $feed->feed_mode == ESS_Database::FEED_MODE_CRON )? TRUE : FALSE ),
									'on'			=> __( 'ON',  'dbem' ),
									'off'			=> __( 'OFF', 'dbem' ),
									'id'			=> 'feed_mode_'.$feed->feed_id,
									'onchecked'		=> "jQuery('#cb-select-".$feed->feed_id."').prop('checked',true);",
									'onunchecked'	=> "jQuery('#cb-select-".$feed->feed_id."').prop('checked',true);"
								) );?>
							</td>
							<td>
								<h4><?php echo sprintf( __("%s ago",'dbem'), human_time_diff( strtotime( $feed->feed_timestamp ) ) ); ?></h4>
							</td>
							<td>
								<h4><?php echo ( ( $feed->feed_mode == ESS_Database::FEED_MODE_CRON && $next_cron != NULL )? sprintf( __("in %s",'dbem'), human_time_diff( $next_cron ) ) : '-' ); ?></h4>
							</td>
							<td>
								<button title="<?php _e( "Reimport the feed to update the event.", 'dbem' );?>" onmousedown="ess_admin.set_event_id('<?php echo $feed->feed_id; ?>');" class="button-primary reload_box" name="update_once" value="<?php echo urlencode( $feed->feed_url );?>" style="background-image:url('<?php echo EM_ESS_URL;?>/assets/img/reload_icon_24x24.png');background-position:7px 2px;background-repeat:no-repeat;"></button>
							</td>
							<td>
								<a href="<?php echo $feed->feed_url; ?>" target="_blank" title="<?php _e( "Download the ESS Feed.",'dbem'); ?>">
									<div class="button-primary arrow_cont">
										<div class="arrow_box"></div>
									</div>
								</a>
							</td>
						</tr><?php
						}
					}
					else
					{
						?><tr>
							<th colspan="8" style="text-align:left;"><?php echo _e( "No Feeds Found",'dbem'); ?></th>
						</tr><?php
					}
				?></tbody>
			</table>

			<?php //self::get_nav_action(); ?>

		</div><?php
	}

	// === SETTINGS TAB ===
	private static function export_page()
	{
		?><div class="em-menu-export em-menu-group">

			<div id="poststuff">


				<!-- Syndication Settings -->
				<div class="postbox" id="em-ess-export-syndication">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e( 'Syndication Settings', 'dbem' ); ?> </span></h3>
					<div class="inside">

						<?php ESS_Elements::get_explain_block(
							"This section defines the way the aggregated feed's events will appears in your event dashboard.".
							"<br />".
							"-"
						 );?>

						<table class="form-table">
							<tbody>
								<?php self::get_checkbox_table_row( array(
									'id'		=> 'ess_syndication_status',
									'title'		=> 'Event Status',
									'explain'	=> "Place automatically if ON is selected the aggregated events as 'publish', otherwise if OFF is selected they will be automatically published as 'draft'."
								));?>
								<?php self::get_checkbox_table_row( array(
									'id'		=> 'ess_backlink_enabled',
									'title'		=> 'Back-link URL',
									'explain'	=> "Place automatically a back-link URL at the end of the event description within the ESS feed to drive web-users to your website."
								));?>
							</tbody>
						</table>

						<p class="submit">
							<input type="submit" class="button-primary" name="save_export" value="<?php _e( 'Save Changes (All)', 'dbem' )?>" />
						</p>
					</div>
				</div>




				<!-- Feed Settings -->
				<div class="postbox" id="em-ess-export-feed">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e( 'Feed Settings', 'dbem' ); ?> </span></h3>
					<div class="inside">

						<?php ESS_Elements::get_explain_block(
							"This section defines the header elements of your ESS feeds.".
							"<br />".
							"Those elements will be read by robot crawlers to identify the origin of the events."
						 );?>

						<table class="form-table">
							<tbody>
								<?php self::get_input_table_row( array(	'id' => 'ess_feed_title', 	'title'	=> 'Feed Title'			));?>
								<?php self::get_input_table_row( array( 'id' => 'ess_feed_rights',  'title'	=> 'Feed Rights' 		));?>
								<?php self::get_input_table_row( array( 'id' => 'ess_feed_website', 'title'	=> 'Feed Website' 	));?>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e('Max. Events per Feed', 'dbem'); ?></th>
									<td>
										<input type="text" name="ess_feed_limit" value="<?php echo ESS_Database::get_option('ess_feed_limit'); ?>" style="width:130px;float:left;clear:right;margin-right:10px;" />
										<em><?php _e('Some ESS aggregators limits the acceptable file size to 2Mo.', 'dbem'); ?></em>
									</td>
								</tr>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e( "Events' Purpose", 'dbem' )?>&nbsp;</th>
									<td>
										<select name="ess_feed_category_type" style="width:130px;float:left;clear:right;margin-right:10px;">
											<option value="0" <?php echo ( get_option('ess_feed_category_type') == '' ) ? 'selected="selected"':''; ?>><?php ucfirst( ESS_Database::DEFAULT_CATEGORY_TYPE ); ?></option>
											<?php foreach( ESS_Control_admin::get_categories_types() as $category_type ): ?>
											<option value="<?php echo $category_type; ?>" <?php echo ((get_option('ess_feed_category_type')==$category_type) ) ? 'selected="selected"':''; ?>><?php echo ucfirst( $category_type ); ?></option>
											<?php endforeach; ?>
										</select>
										<em><?php _e('Defines the global theme of all your events (used by search engines)', 'dbem'); ?></em>
									</td>
								</tr>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e( "Events' Currency", 'dbem' )?>&nbsp;</th>
									<td>
										<select name="ess_feed_currency" style="width:130px;float:left;clear:right;margin-right:10px;">
											<option value="0" <?php echo ( get_option('ess_feed_currency') == '' ) ? 'selected="selected"':''; ?>><?php ESS_Database::DEFAULT_CURRENCY; ?></option>
											<?php foreach( FeedValidator::$CURRENCIES_ as $country => $currency ): ?>
											<option value="<?php echo $currency; ?>" <?php echo ((get_option('ess_feed_currency')==$currency) ) ? 'selected="selected"':''; ?>><?php echo $currency; ?></option>
											<?php endforeach; ?>
										</select>
										<em><?php _e("Defines the currency used to set the events' prices", 'dbem'); ?></em>
									</td>
								</tr>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e ( 'Feed Language:', 'dbem' )?>&nbsp;</th>
									<td>
										<select name="ess_feed_language" style="max-width:402px;">
											<option value="0" <?php echo ( get_option('ess_feed_language') == '' ) ? 'selected="selected"':''; ?>><?php _e('none selected','dbem'); ?></option>
											<?php foreach( FeedValidator::$LANGUAGES_ as $lang_code => $lang_name): ?>
											<option value="<?php echo $lang_code; ?>" <?php echo ((get_option('ess_feed_language')==$lang_code) ) ? 'selected="selected"':''; ?>><?php echo $lang_name; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e('Events Time Zone', 'dbem'); ?></th>
									<td><?php self::get_timezone_picker(); ?></td>
								</tr>
							</tbody>
						</table>
						<p class="submit">
							<input type="submit" class="button-primary" name="save_export" value="<?php _e( 'Save Changes (All)', 'dbem' )?>" />
						</p>
					</div>
				</div>



				<!-- Feed Visibility -->
				<div class="postbox" id="em-ess-export-visibility">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e( 'Feed Visibility', 'dbem' ); ?> </span></h3>
					<div class="inside">

						<?php ESS_Elements::get_explain_block(
							"This section control the visibility of the ESS feeds and its components.".
							"<br />".
							"You can define if the search engines can automaticaly access and analyse your events."
						 );?>

						<table class="form-table">
							<tbody>

								<tr><th colspan="3"><strong><?php _e("Website Feed Visibility","dbem");?> </strong></th></tr>
								<?php self::get_checkbox_table_row( array(
									'id'		=> 'ess_feed_visibility_web',
									'title'		=> 'Feed Icon',
									'explain'	=> 'Display the ESS feeds icon on your website to allow other web reader to syndicate to your events.'
								));?>
								<?php self::get_checkbox_table_row( array(
									'id'		=> 'ess_feed_visibility_meta',
									'title'		=> 'Meta Data',
									'explain'	=> 'Display all your public events in the header meta tags of your website (use by search engines for web indexation).'
								));?>
								<?php self::get_checkbox_table_row( array(
									'id'		=> 'ess_feed_push',
									'title'		=> 'Search Engines',
									'explain'	=> 'Push your events to search engine in real-time at every changes.'
								));?>


								<tr><th colspan="3"><strong><?php _e("Feed's Elements Visibility","dbem");?> </strong></th></tr>
								<?php self::get_checkbox_table_row( array(	'id' => 'ess_feed_import_images',
									'title'		=> 'Import Images',
									'explain'	=> 'Defines if the images have to be also imported while importing an ESS feed.'
								));?>
								<?php self::get_checkbox_table_row( array(	'id' => 'ess_feed_export_images',
									'title'		=> 'Export Images',
									'explain'	=> 'Defines if your images have to be exported in your ESS feeds.'
								));?>
								<!--
								<?php self::get_checkbox_table_row( array(	'id' => 'ess_feed_import_videos',
									'title'		=> 'Import Videos',
									'explain'	=> 'Defines if the video files have to be also imported while importing an ESS feed.'
								));?>
								<?php self::get_checkbox_table_row( array(	'id' => 'ess_feed_export_videos',
									'title'		=> 'Export Videos',
									'explain'	=> 'Defines if your video files have to be exported in your ESS feeds.'
								));?>
								<?php self::get_checkbox_table_row( array(	'id' => 'ess_feed_import_sounds',
									'title'		=> 'Import Sounds',
									'explain'	=> 'Defines if the audio files have to be also imported while importing an ESS feed.'
								));?>
								<?php self::get_checkbox_table_row( array(	'id' => 'ess_feed_export_sounds',
									'title'		=> 'Export Sounds',
									'explain'	=> 'Defines if your audio files have to be exported in your ESS feeds.'
								));?>
								-->
							</tbody>
						</table>

						<p class="submit">
							<input type="submit" class="button-primary" name="save_export" value="<?php _e( 'Save Changes (All)', 'dbem' )?>" />
						</p>
					</div>
				</div>



				<!-- Events Organizer -->
				<div class="postbox" id="em-ess-export-owner">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Events Organizer', 'dbem' ); ?> </span></h3>
					<div class="inside">

						<?php ESS_Elements::get_explain_block(
							"This section defines the organizer information displayed in every ESS feeds.".
							"<br />".
							"This information will be available for any third party that syndicate to your feeds."
						);?>

						<table class="form-table">
							<tbody>
								<tr>
									<th><strong><?php _e('Display Organizer', 'dbem'); ?></strong></th>
									<td>
										<?php ESS_Elements::button_checkbox( array( 'id' => 'ess_owner_activate', 'checked' => get_option('ess_owner_activate'), 'on'=>'ON', 'off'=>'OFF' ) );?>
										<em><?php _e('Display, in the feed, the coordinate of the event organizer (will be the same for all the events).', 'dbem'); ?></em>
									</td>
								</tr>
							</tbody>
						</table>
						<table class="form-table" id="block_owner" style="opacity:<?php echo get_option('ess_owner_activate')?'1':'0.3';?>;">
							<tbody>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e ( 'First Name / Last Name:', 'dbem' )?>&nbsp;</th>
									<td>
										<input type="text" name="ess_owner_firstname" value="<?php echo ESS_Database::get_option('ess_owner_firstname'); ?>" style="width:49%;" />
										<input type="text" name="ess_owner_lastname" value="<?php echo ESS_Database::get_option('ess_owner_lastname'); ?>" style="width:49%;" />
									</td>
								</tr>
								<?php self::get_input_table_row( array( 'id' => 'ess_owner_company', 'title' => 'Company / Organisation' ));?>
								<?php self::get_input_table_row( array( 'id' => 'ess_owner_address', 'title' => 'Address:' ));?>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e ( 'City / State / Postcode:', 'dbem' )?>&nbsp;</th>
									<td>
										<input type="text" name="ess_owner_city" value="<?php echo ESS_Database::get_option('ess_owner_city'); ?>" style="width:44%;" />
										<input type="text" name="ess_owner_state" value="<?php echo ESS_Database::get_option('ess_owner_state'); ?>" style="width:43%;" />
										<input type="text" name="ess_owner_zip" value="<?php echo ESS_Database::get_option('ess_owner_zip'); ?>" style="width:10%;" />
									</td>
								</tr>
								<tr>
									<td width="20">&nbsp;</td>
									<th><?php _e ( 'Country:', 'dbem' )?>&nbsp;</th>
									<td>
										<select name="ess_owner_country">
											<option value="0" <?php echo ( get_option('ess_owner_country') == '' ) ? 'selected="selected"':''; ?>><?php _e('none selected','dbem'); ?></option>
											<?php foreach( em_get_countries() as $country_key => $country_name): ?>
											<option value="<?php echo $country_key; ?>" <?php echo ((get_option('ess_owner_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo $country_name; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>
								<?php self::get_input_table_row( array( 'id' => 'ess_owner_website', 'title' => 'Website:' ));?>
								<?php self::get_input_table_row( array( 'id' => 'ess_owner_phone', 'title' => 'Phone:' ));?>
							</tbody>
						</table>
						<p class="submit">
							<input type="submit" class="button-primary" name="save_export" value="<?php _e( 'Save Changes (All)', 'dbem' )?>" />
						</p>
					</div>
				</div>



				<!-- Social Platforms -->
				<div class="postbox" id="em-ess-export-social">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( 'Social Platform', 'dbem' ); ?> </span></h3>
					<div class="inside">

						<?php ESS_Elements::get_explain_block(
							"This section defines the social platforms you want to link with all your events.".
							"<br />" .
							"This information will be available for search engine for high-ranking your pages by creating a cross-link."
						 );?>

						<?php foreach ( ESS_Database::$SOCIAL_PLATFORMS as $type => $socials_ ) : ?>
							<div class="postbox" id="em-ess-export-social">
								<div class="handlediv" title="<?php _e( 'Click to toggle', 'dbem'); ?>"><br /></div><h3><span><?php _e ( ucfirst( $type ), 'dbem' ); ?> </span></h3>
								<div class="inside">
									<table class="form-table">
										<tbody>
											<?php foreach ( $socials_ as $social ) : ?>
												<?php self::get_social_table_row( $social );?>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						<?php endforeach; ?>

						<p class="submit">
							<input type="submit" class="button-primary" name="save_export" value="<?php _e( 'Save Changes (All)', 'dbem' )?>" />
						</p>
					</div>
				</div>



			</div>


		</div><?php
	}


	private static function get_social_table_row( $social='' )
	{
		?><tr>
			<td width="20">&nbsp;</td>
			<th><?php _e( ucfirst( $social ), 'dbem' ); ?></th>
			<td><input type="text" class="input-social-icon icon-<?php echo $social; ?>" name="ess_social_<?php echo $social; ?>" value="<?php echo ESS_Database::get_option('ess_social_' . $social ); ?>"/></td>
		</tr><?php
	}

	private static function get_checkbox_table_row( Array $DATA_=NULL )
	{
		$title		= ( ( isset( $DATA_['title'] 	) )? $DATA_['title'] 	: '' );
		$id 		= ( ( isset( $DATA_['id'] 		) )? $DATA_['id'] 		: '' );
		$explain	= ( ( isset( $DATA_['explain'] 	) )? $DATA_['explain'] 	: '' );

		?><tr>
			<td width="20">&nbsp;</td>
			<th><?php _e( $title, 'dbem' ); ?></th>
			<td>
				<?php ESS_Elements::button_checkbox( array( 'id' => $id, 'checked' => get_option( $id ), 'on'=>'ON', 'off'=>'OFF' ) );?>
				<em><?php _e( $explain, 'dbem' ); ?></em>
			</td>
		</tr><?php
	}

	private static function get_input_table_row( Array $DATA_=NULL )
	{
		?><tr>
			<td width="20">&nbsp;</td>
			<th><?php _e( $DATA_['title'], 'dbem'); ?></th>
			<td><input type="text" name="<?php echo $DATA_['id']; ?>" value="<?php echo ESS_Database::get_option( $DATA_['id'] ); ?>" /></td>
		</tr><?php
	}

	private static function get_timezone_picker()
	{
		?><div id="timezone-picker" class="hide-if-no-js">
		  <img class="hide-if-no-js" id="timezone-image" src="<?php echo EM_ESS_URL;?>/assets/img/time_zone_400x200.png" width="400" height="200" usemap="#timezone-map" />
		  <img class="timezone-pin hide-if-no-js" src="<?php echo EM_ESS_URL;?>/assets/img/map_pin_7x7.png" style="padding-top:4px;" />
		  <map name="timezone-map" id="timezone-map" class="hide-if-no-js">
		  	<?php foreach ( ESS_Timezone::$WORLD_TIMEZONES_ as $TZ_ ) : ?>
		  		<area data-timezone="<?php echo $TZ_['timezone'];?>" data-country="<?php echo $TZ_['country'];?>" data-pin="<?php echo $TZ_['pin'];?>" data-offset="<?php echo $TZ_['offset'];?>" shape="<?php echo $TZ_['shape'];?>" coords="<?php echo $TZ_['coords'];?>" />
		    <?php endforeach ?>
		  </map>
		</div>
		<fieldset id="timezone-fieldset">
			<h4 id="current-timezone"><?php echo get_option( 'ess_feed_timezone');?></h4>
			<input type="button" id="timezone-detect" value="Auto Detect Timezone" class="button-secondary hide-if-no-js" /><br/>
			<select id="edit-date-default-timezone" name="ess_feed_timezone">
				<option value=""><?php _e('none selected','dbem'); ?></option>
				<?php foreach ( ESS_Timezone::$WORLD_TIMEZONES_ as $TZ_ ) : ?>
			  		<option value="<?php echo $TZ_['timezone'];?>" <?php echo ((get_option('ess_feed_timezone')==$TZ_['timezone'])?"selected='selected'":""); ?>><?php echo $TZ_['timezone'];?></option>
			    <?php endforeach ?>
			</select>
		</fieldset><?php
	}

}