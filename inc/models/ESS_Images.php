<?php
/**
  * Controller ESS_Images
  * Control the user interaction to assign images to the events through the importation of ESS feed
  *
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */
final class ESS_Images
{
	function __construct(){}



	public static function add( Array $DATA_=NULL )
	{
		$url 		= $DATA_['uri'];
		$name		= @$DATA_['name'];
		$post_id	= @$DATA_['post_id'];

		apply_filters( 'em_object_get_image_type', 'event', false, ( isset( $this ) )? $this : self );

		//Handle the attachment as a WP Post
		$attachment = '';
		$tmpfname = tempnam( ESS_IO::tmp(), "ESS_IMG_" . $post_id . "_" );

		$h = fopen( $tmpfname, "w" );

		if ( $h && strlen( $url ) > 5 )
		{
			fwrite( $h, file_get_contents( $url ) );

			$image_ = getimagesize( $tmpfname );

			switch ( $image_['mime'] ) {
			    case "image/gif": 	$mime = "gif"; break;
			    case "image/jpeg":  $mime = "jpg"; break;
			    case "image/png": 	$mime = "png"; break;
			    case "image/bmp": 	$mime = "bmp"; break;
			}

			$file = array(
				'tmp_name' 	=> $tmpfname,
				'name'		=> basename( $tmpfname ),
				'type'		=> $image_[2],
				'mime'		=> $mime,
				'width'		=> $image_[0],
				'height'	=> $image_[1],
				'size'		=> filesize( $tmpfname ),
				'error'		=> 0
			);

			//var_dump( $file );

			if ( file_exists( $file[ 'tmp_name' ] ) && ESS_Images::image_validator( $file ) )
			{
				require_once( ABSPATH . "wp-admin" . '/includes/file.php'  );
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );

				$attachment = ESS_Images::handle_upload( $file );

				if ( $attachment )
				{
					//echo "DEBUG: <b>". __CLASS__.":".__LINE__."</b>";
					//var_dump( $attachment );

					$attachment_id = wp_insert_attachment( array(
						'post_mime_type' 	=> $image_['mime'], //$attachment['type'],
						'post_title' 		=> $name,
						'post_content' 		=> '',
						'post_status' 		=> 'inherit'
					), $attachment['file'], $post_id );

					$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $attachment[ 'file' ] );

					wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

					update_post_meta( $post_id, '_thumbnail_id', 	$attachment_id );
					update_post_meta( $post_id, '_start_ts', 		date('U') );
					update_post_meta( $post_id, '_end_ts', 			intval( date('U')+(60*60*24*365) ) );

					@fclose( $h );
					if ( file_exists( $tmpfname ) )
						@unlink( $tmpfname );

					return apply_filters( 'em_object_image_upload', $attachment_id, $this );
				}
			}
			@fclose( $h );
			if ( file_exists( $tmpfname ) )
				@unlink( $tmpfname );
		}

		return apply_filters( 'em_object_image_upload', false, $this);
	}

	private static function handle_upload( &$file )
	{
		global $ESS_Notices;

		$file = apply_filters( 'wp_handle_upload_prefilter', $file );

		if ( isset( $file[ 'error' ] ) && !is_numeric( $file[ 'error' ] ) && $file[ 'error' ] )
			$ESS_Notices->add_error( $file[ 'error' ] );

		$time = current_time('mysql');

		extract( wp_check_filetype_and_ext( $file[ 'tmp_name' ], $file[ 'name' ], false ) );

		$ext	= ( ( !$ext  )? $file['mime'] : ltrim( strrchr( $file['name'], '.' ), '.' ) );
		$type 	= ( ( !$type )? $file['type'] : $type );

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			$ESS_Notices->add_error( sprintf( __( 'Sorry, this file type is not permitted for security reasons (%s or %s).' ), $type, $ext ) );

		if ( !( ( $uploads = wp_upload_dir( $time ) ) && $uploads[ 'error' ] === false ) )
			$ESS_Notices->add_error( $uploads[ 'error' ] );

		//var_dump( $uploads );

		//echo "ABSPATH: ". ABSPATH;

		$filename = wp_unique_filename( $uploads['path'], $file['name'], null );

		// Move the file to the uploads dir
		$new_file = $uploads['path'] . "/". $filename . "." . $ext;

		//if ( move_uploaded_file( $file['tmp_name'], $new_file ) === false )
		if ( rename( $file['tmp_name'], $new_file ) === false )
		{
			$ESS_Notices->add_error( sprintf(
				__('The uploaded file could not be moved to %s.' ),
				( ( strpos( $uploads['basedir'], ABSPATH ) === 0 )?
					str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir']
					:
					basename( $uploads['basedir'] ) . $uploads['subdir']
				)
			) );
		}

		// Set correct file permissions
		$stat = stat( dirname( $new_file ) );
		$perms = $stat['mode'] & 0000666;
		@chmod( $new_file, $perms );

		if ( is_multisite() ) delete_transient( 'dirsize_cache' );

		return apply_filters( 'wp_handle_upload', array(
			'file' 	=> $new_file,
			'url' 	=> $uploads[ 'url' ] . "/" . $filename . "." . $ext,
			'type' 	=> $type
		), 'upload' );
	}

	private static function image_validator( $file=NULL )
	{
		$ERRORS_ = array();

		if ( !empty( $file ) && $file[ 'size' ] > 0 )
		{
			$mime_types 	= array(1 => 'gif', 2 => 'jpg', 3 => 'png' );
			$maximum_size 	= get_option( 'dbem_image_max_size' );
			$maximum_width 	= get_option( 'dbem_image_max_width' );
			$maximum_height = get_option( 'dbem_image_max_height' );
			$minimum_width 	= get_option( 'dbem_image_min_width' );
			$minimum_height = get_option( 'dbem_image_min_height' );

			if (   $file['size']  > $maximum_size ) 											{ array_push( $ERRORS_, sprintf( __( 'The image file is too big! Maximum size: %s', 	'dbem' ), $maximum_size ) ); }
		  	if ( ( $file['width'] > $maximum_width) || ( $file['height'] > $maximum_height ) ) 	{ array_push( $ERRORS_, sprintf( __( 'The image is too big! Maximum size allowed: %s',	'dbem' ), $maximum_width ." x ". $maximum_height ) ); }
			if ( ( $file['width'] < $minimum_width) || ( $file['height'] < $minimum_height ) ) 	{ array_push( $ERRORS_, sprintf( __( 'The image is too small! Minimum size allowed: %s','dbem' ), $minimum_width ." x ". $minimum_height ) ); }
		  	if ( empty( $file['type'] ) || !array_key_exists( $file['type'], $mime_types ) ) 	{ array_push( $ERRORS_, __( 'The image is in a wrong format!',							'dbem' ) ); }
  		}

		if ( @count( $ERRORS_ ) > 0 )
		{
			global $ESS_Notices;
			$ESS_Notices->add_error( $ERRORS_ );
		}

		return apply_filters( 'em_object_image_validate', ( @count( $ERRORS_ ) <= 0 )? true : false, ( isset( $this ) )? $this : self );
	}

	public static function delete( $post_id=NULL, $attachement_id_to_delete=0 )
	{
		if ( $post_id != NULL )
		{
			$images_ =& get_children( array (
				'post_parent' 		=> $post_id,
				'post_type' 		=> 'attachment',
				'post_mime_type' 	=> 'image'
			));

			//echo "DEBUG: <b>". __CLASS__.":".__LINE__."</b><br/>";
			//var_dump( $images_ );die;

			if ( !empty( $images_ ) )
			{
				foreach ( $images_ as $attachment_id => $attachment )
				{
					//echo "attachement_id: " . $attachment_id . "<br/>";
					//echo "<img src='".wp_get_attachment_thumb_url($attachment_id)."' />";
					if ( $attachement_id_to_delete > 0 )
					{
						if ( $attachement_id_to_delete == $attachment_id )
							wp_delete_attachment( $attachment_id );
					}
					else wp_delete_attachment( $attachment_id );
				}
			}
			return true;
		}
		return false;
	}

	public static function get( $post_id=NULL )
	{
		$URI_ = array();

		if ( $post_id != NULL )
		{
			$meta_ = get_post_meta( $post_id, '_thumbnail_id' );

			if ( @count( $meta_ ) )
			{
				foreach ( $meta_ as $attachment_id )
				{
					$post_ = get_post( $attachment_id );

					if ( FeedValidator::isValidMediaURL( $post_->guid, 'image' ) )
						array_push( $URI_, array(
							'name' 	=> $post_->post_title,
							'uri' 	=> $post_->guid
						) );
				}
			}

			$images_ =& get_children( array (
				'post_parent' 		=> $post_id,
				'post_type' 		=> 'attachment',
				'post_mime_type' 	=> 'image'
			));

			if ( !empty( $images_ ) )
			{
				foreach ( $images_ as $attachment_id => $attachment )
				{
					array_push( $URI_, array(
						'uri'  => wp_get_attachment_url( $attachment_id ),
						'name' => wp_get_attachment_image( $attachment_id, 'thumbnail' )
					));
				}
			}
		}

		foreach ( $URI_ as $img_ )
		{
			$duplicate = 0;
			foreach ( $URI_ as $i => $img_control_ )
			{
				if ( $img_['uri'] == $img_control_['uri'] )
				{
					$duplicate++;
					if ( $duplicate > 1 )
						$URI_ = array_slice( $URI_, $i, 1 );
				}
			}
		}

		//dd( $URI_ );

		return $URI_;
	}

	public static function get_thumbnails( $post_id=NULL )
	{
		$URI_ = array();

		if ( $post_id != NULL )
		{
			$images_ = @get_children( array (
				'post_parent' 		=> $post_id,
				'post_type' 		=> 'attachment',
				'post_mime_type' 	=> 'image'
			));

			if ( !empty( $images_ ) )
			{
				foreach ( $images_ as $attachment_id => $attachment )
				{
					$img_ = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
					array_push( $URI_, array(
						'url' 		=> $img_[0],
						'width'		=> $img_[1],
						'height'	=> $img_[2]
					) );
				}
			}
		}
		return $URI_;
	}



}