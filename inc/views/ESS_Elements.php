<?php
/**
  * View ESS_Elements
  * Container of recyclebale graphical sub-elements used within the ESS plugin
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  */
final class ESS_Elements
{
	function __construct(){}

	public static function get_feed_meta_header()
	{
		?><link rel="alternate" type="application/ess+xml" title="<?php _e('ESS Feed of all the events published in the website and available for syndication.','dbem').' ('.ESS_IO::ESS_WEBSITE .')'; ?>"  href="<?php echo ESS_IO::get_feed_url(); ?>" /><?php
	}

	public static function get_feed_meta_link()
	{
		?><li>
			<a href="<?php echo ESS_IO::get_feed_url(); ?>" target="_blank" title="<?php _e('ESS Feed of all the events published in the website and available for syndication.','dbem');?>">
				<?php _e( "Events ESS",'dbem' ); ?>
			</a>
		</li><?php
	}


	private static function get_ess_image( $event_id='' )
	{
		return "<a href='".ESS_IO::get_feed_url( $event_id )."' target='_blank' title='".__('ESS Feed of all the events published in the website and available for syndication.','dbem')."'>".
			"<img src='".EM_ESS_URL."/assets/img/ESS_logo_16x16.png' title='" . __( "Events content with ESS Feed format", 'dbem' ) ."' style='margin-right:6px;'/>".
		"</a>";
	}

	private static function get_hypecal_signature()
	{
		return "<p style='color:rgb(153,153,153);font-size:11px;padding:0;margin:0;display:inline;'>" .
			__( "Events available in ", 'dbem' ) .
			" <a href='".ESS_IO::HYPECAL_WEBSITE."' target='_blank' alt='" . __( "Hypecal Events Search Engine", 'dbem' )."'>" .
				__( "Hypecal", 'dbem' ) .
			"</a>".
		"</p>";
	}



	public static function get_output_single( $content='' )
	{
		global $EM_Event;

		return $content .
		//( ( get_option( 'ess_feed_push' ) )?self::get_hypecal_signature() : '' ).
		( ( $EM_Event instanceof EM_Event && get_option( 'ess_feed_visibility_web', TRUE ) )?
			'<div style="float:left;margin:0 5px 0 0;">' .
				self::get_ess_image( $EM_Event->event_id ) .
			'</div>'
			:
			""
		);
	}

	public static function get_listing_content( $content='' )
	{
		$separator = '<p style="color:#999; font-size:11px;">';

		return str_replace( $separator,
			//( ( get_option( 'ess_feed_push' ) )? self::get_hypecal_signature() : '' ).
			( ( get_option( 'ess_feed_visibility_web', TRUE ) )?
				'<div style="float:right;width:16px;height:16px;margin:0 5px 0 0;">' .
					self::get_ess_image() .
				'</div>'
				: ''
			) . $separator,
			$content
		);
	}




	public static function get_explain_block( $txt='' )
	{
		?><div class="ess_explain">
			<img src="<?php echo EM_ESS_URL. '/assets/img/info_icon_30x30.png'; ?>" alt="<?php _e('Info','dbem');?>" />
			<p><?php _e( $txt, "dbem" ); ?></p>
		</div><?php
	}

	public static function button_checkbox( Array $DATA_=NULL )
	{
		if(empty($DATA_))return;
		?><div
			class			= "toggle hide-if-no-js iphone <?php echo ( ( $DATA_['checked'] == true )? 'on' : 'off' ); ?>"
			id				= "<?php echo $DATA_['id']; ?>-div"
			data-checkbox	= "<?php echo $DATA_['id']; ?>-checkbox"
			data-ontext		= "<?php echo $DATA_['on']; ?>"
			data-offtext	= "<?php echo $DATA_['off']; ?>">
			<?php echo ( ( $DATA_['checked'] == true )? $DATA_['on'] : $DATA_['off'] );	?>
		</div>
		<input
			<?php if(isset($DATA_['onchecked'])||isset($DATA_['onunchecked'])) : ?>
				onchange = "if(jQuery(this).is(':checked')){<?php echo $DATA_['onchecked'];?>}else{<?php echo $DATA_['onunchecked']; ?>};"
			<?php endif; ?>
			type	= "checkbox"
			class	= "<?php echo $DATA_['id']; ?>-checkbox input-checkbox"
			name	= "<?php echo $DATA_['id']; ?>"
			<?php echo( ( $DATA_['checked'] == true )? "checked='checked'" : '' );?>
		/>
    	<?php
	}

	public static function get_events_manager_required()
	{
		?><div class="update-nag">
			<?php _e('Install','dbem'); ?>
			<a href="<?php echo ESS_IO::PLUGIN_WEBSITE; ?>" target="_blank">
				<?php _e( 'Events Manager', 'dbem' ); ?>
			</a>
		</div><?php
		die;
	}

	public static function get_php_curl_required()
	{
		?><div class="update-nag">
			<?php _e( "PHP cURL must be installed on your server: ", 'dbem' ); ?>
			<?php echo self::get_curl_lib_link(); ?>
		</div><?php
		die;
	}

	public static function get_curl_lib_link()
	{
		return "<a href='".ESS_IO::CURL_LIB_URL."' target='_blank'>" .
			__( "Client URL Library", 'dbem' ) .
		"</a>";
	}

	public static function get_ahref( $url )
	{
		return "<a href='".$url."' target='_blank'>" .$url ."</a>";
	}


	/* DEBUG FUNCTION */
	public static function get_html_var_dump()
 	{
		ob_start();
		call_user_func_array( 'var_dump', func_get_args() );
		return ob_get_clean();
  	}
}