<?php
//error_reporting(E_ALL); // DEBUG
if ( function_exists( 'mb_detect_order' ) )
	mb_detect_order( "UTF-8,eucjp-win,sjis-win" );

require_once( "EssDTD.php" );
require_once( 'FeedValidator.php' );
require_once( 'EventFeed.php' );

 /**
  * Universal ESS Feed Writer class
  * Generate ESS Feed v0.9
  *
  * @package 	ESSFeedWriter
  * @author  	Brice Pissard
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed
  */
final class FeedWriter
{
	const LIBRARY_VERSION	= '1.5';	// GitHub library versioning control.
	const ESS_VERSION		= '0.9'; 	// ESS Feed version.
	const CHARSET			= 'UTF-8';	// Defines the encoding Chartset for the whole document and the value inserted.
	public $lang			= 'en';		// Default 2 chars language (ISO 3166-1).

	public $DEBUG			= FALSE;	// output debug information.
	public $AUTO_PUSH		= TRUE;		// Defines feed changes have to be submited to ESS Aggregators.
	public $IS_DOWNLOAD		= FALSE;	// Defines if the feed is to be downloaded (with Header: application/ess+xml).
	const EMAIL_UP_TO_DATE	= TRUE;		// Defines if an email is sent to system administrator if the version is not up-to-date.
	const REPLACE_ACCENT	= FALSE;	// if some problems occured durring encoding/decoding the data into UTF8, this parameter set to TRUE force the replacement of åççéñts by accents.

	private $channel 		= array();  // Collection of Channel elements.
	private $items			= array();  // Collection of items as object of FeedItem class.
	const TB				= '   ';	// Display a tabulation (for humans).
	const LN				= '
';										// Display breaklines (for humans).

	/**
	 * FeedWriter Class Constructor
	 *
	 * @access 	public
	 * @param  	String 	[OPTIONAL] 2 chars language (ISO 3166-1) definition for the current feed.
	 * @param  	Array 	[OPTIONAL] array of event's feed tags definition.
	 * @return 	void
	 */
	function __construct( $lang='en', $data_=NULL )
	{
		if ( function_exists( 'set_error_handler' ) )
			set_error_handler( array( 'FeedWriter', 'error_handler' ) );

		$channelDTD = EssDTD::getChannelDTD(); // DTD Array of Channel first XML child elements.

		$this->lang = ( strlen( $lang ) == 2 )? strtolower( $lang ) : $this->lang;

		$this->setGenerator( 'ess:php:generator:version:' . self::LIBRARY_VERSION );

		$mandatoryRequiredCount = 0;
		$mandatoryCount 		= 0;

		if ( $data_ != NULL )
		{
			if ( count( $data_ ) > 0 )
			{
				foreach ( $data_ as $key => $el )
				{
					switch ( $key )
					{
						case 'title':		$this->setTitle( 	  $el ); if ( $channelDTD[ $key ] == TRUE ) $mandatoryCount++; break;
						case 'link':		$this->setLink(  	  $el ); if ( $channelDTD[ $key ] == TRUE ) $mandatoryCount++;$mandatoryCount++; break; // + element ID
						case 'published':	$this->setPublished(  $el ); if ( $channelDTD[ $key ] == TRUE ) $mandatoryCount++; break;
						case 'updated':		$this->setUpdated(    $el ); if ( $channelDTD[ $key ] == TRUE ) $mandatoryCount++; break;
						case 'generator':	$this->setGenerator(  $el ); if ( $channelDTD[ $key ] == TRUE ) $mandatoryCount++; break;
						case 'rights':		$this->setRights(     $el ); if ( $channelDTD[ $key ] == TRUE ) $mandatoryCount++; break;

						default: throw new Exception("Error: XML Channel element < ".$key." > is not defined within ESS DTD." ); break;
					}
				}

				foreach ( $channelDTD as $kk => $val )
				{
					if ( $val == TRUE && $kk != 'feed' )
						$mandatoryRequiredCount++;
				}

				if ( $mandatoryRequiredCount != $mandatoryCount || $mandatoryCount == 0 )
				{
					$out = '';
					foreach ( $channelDTD as $key => $m)
					{
						if ( $m == TRUE )
							$out .= "< $key >, ";
					}
					throw new Exception( "Error: All XML Channel's mandatory elements are required: ". $out );
				}
			}
		}
	}


	private function t( $num )
	{
		$text = "";

		for ( $i=1; $i <= $num ; $i++ )
			$text .= self::TB;

		return $text;
	}

	/**
	 * Set a channel element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String  name of the channel tag.
	 * @param   String  content of the channel tag.
	 * @return  void
	 */
	private function setChannelElement( $elementName, $content )
	{
		if ( is_string( $content ) )
			$content = FeedValidator::xml_entities( $content, self::CHARSET );

		$this->channel[ $elementName ] = $content;
	}

	/**
	 * 	Genarate the ESS Feed On-the-fly or create a file on local server disk.
	 * 	If the feed is generated and record on the server it consume less load on PHP and Database resources.
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function genarateFeed( $filePath='', $displayResult=TRUE )
	{
		if ( function_exists( 'mb_internal_encoding' ) )
			mb_internal_encoding( self::CHARSET );

		if ( $this->DEBUG == FALSE && $displayResult == TRUE )
		{
			if ( function_exists( 'header_remove' ) )
				header_remove();

			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			header( "Cache-Control: no-cache" );
			header( "Pragma: no-cache" );
			header( "Keep-Alive: timeout=1, max=1" );

			if ( $this->IS_DOWNLOAD ) { header( 'Content-Type: application/ess+xml; charset=' .self::CHARSET ); }
			else					  { header( 'Content-Type: text/xml; charset=' .self::CHARSET ); }
		}

		$feedData = $this->getFeedData();

		//var_dump( $feedData );

		if ( FeedValidator::isNull( $filePath ) == FALSE && $this->DEBUG == FALSE )
			$this->genarateFeedFile( $filePath, $feedData );

		if ( function_exists( 'restore_error_handler' ) )
			restore_error_handler();

		if ( $displayResult == TRUE )
			echo $feedData;
		else
			return $feedData;
	}

	/**
	 * Genarate the ESS File
	 *
	 * @access 	public
	 * @param	String	Local server path where the feed will be stored.
	 * @param	URL		URL of the same feed but available online. this URL will be used to broadcast your event to events search engines.
	 * @return	void
	 */
	public function genarateFeedFile( $filePath='', $feedData=NULL )
	{
		if ( function_exists( 'mb_internal_encoding' ) )
			mb_internal_encoding( self::CHARSET );

		try
		{
			$fp = fopen( $filePath, 'w' );

			if ( $fp !== FALSE )
			{
				fwrite( $fp, ( ( $feedData != NULL )? $feedData : $this->getFeedData() ) );
				fclose( $fp );
			}
		}
		catch( ErrorException $error )
		{
			throw new Exception( "Error: Impossible to generate the ESS file on local server disk: " . $error );
			return;
		}
	}

	/**
	 * Get ESS Feed data in String format.
	 *
	 * @access  public
	 * @return  String
	 */
	public function getFeedData()
	{
		$out = "";

		$out .= $this->getHead();
		$out .= $this->getChannel();
		$out .= $this->getItems();
		$out .= $this->getEndChannel();

		$this->pushToAggregators( '', $out );

		return $out;
	}

	/**
	 * Create a new EventFeed.
	 *
	 * @access  public
	 * @return 	Object  instance of EventFeed class
	 */
	public function newEventFeed( Array $arr_= NULL )
	{
		$newEvent = new EventFeed( NULL, self::CHARSET, self::REPLACE_ACCENT );

		if ( $arr_ )
		{
			if ( count( $arr_ ) > 0 )
			{
				if ( isset( $arr_['title'] 		) ) { if ( FeedValidator::isNull( 	   $arr_['title'] 		) == FALSE ) { $newEvent->setTitle( 		$arr_['title'] 			); }}
				if ( isset( $arr_['uri'] 		) ) { if ( FeedValidator::isNull( 	   $arr_['uri'] 		) == FALSE ) { $newEvent->setUri( 			$arr_['uri'] 			); }}
				if ( isset( $arr_['published'] 	) ) { if ( FeedValidator::isValidDate( $arr_['published'] 	) == TRUE  ) { $newEvent->setPublished( 	$arr_['published'] 		); } else { $newEvent->setPublished( self::getISODate() ); }}
				if ( isset( $arr_['updated'] 	) ) { if ( FeedValidator::isValidDate( $arr_['updated'] 	) == TRUE  ) { $newEvent->setUpdated( 		$arr_['updated'] 		); }}
				if ( isset( $arr_['access'] 	) ) { if ( FeedValidator::isNull( 	   $arr_['access'] 		) == FALSE ) { $newEvent->setAccess( 		$arr_['access'] 		); } else { $newEvent->setAccess( EssDTD::ACCESS_PUBLIC ); }}
				if ( isset( $arr_['description']) ) { if ( FeedValidator::isNull(	   $arr_['description']	) == FALSE ) { $newEvent->setDescription(	$arr_['description'] 	); }}
				if ( isset( $arr_['tags'] 		) ) { if ( count( $arr_['tags'] ) > 0 ) 								 { $newEvent->setTags(			$arr_['tags'] 			); }}
			}
		}
		return $newEvent;
	}

	/**
	 * Add a EventFeed to the main class
	 *
	 * @access 	public
	 * @param  	Object  instance of EventFeed class
	 * @return 	void
	 */
	public function addItem( $eventFeed )
	{
		$this->items[] = $eventFeed;
	}


	public static function getCurrentURL()
	{
		$protocol 	= ( ( isset( $_SERVER[ 'SERVER_PROTOCOL' ] 	) )? ( ( stripos( $_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === TRUE )? 'https://' : 'http://' ) : 'http://' );
		$host		= ( ( isset( $_SERVER[ 'HTTP_HOST' ]		) )? $_SERVER[ 'HTTP_HOST' ] 	: '' );
		$request 	= ( ( isset( $_SERVER[ 'REQUEST_URI' ]		) )? $_SERVER[ 'REQUEST_URI' ]	: '' );

		return $protocol . $host . $request;
	}






	// -------------------------------------
	// -- Getter/Setter Wrapper Functions -------------------------------------------------------------------
	// -------------------------------------



	/**
	 * Set the 'title' channel element
	 *
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param  	String  value of 'title' channel tag.
	 * 					Define the language-sensitive feed title.
	 * 					Should not be longer then 128 characters.
	 * @return  void
	 */
	public function setTitle( $el=NULL )
	{
		$elNane = 'title';
		if ( self::controlChannelElements( $elNane, $el ) )
		{
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );

			if ( !isset( $this->channel[ 'id' ] ) || FeedValidator::isNull( $this->channel[ 'id' ] ) )
				$this->setId( $el );
		}
		else throw new Exception( "Error: '< channel >< $elNane>' XML element is mandatory and can not be empty." );
	}
	public function getTitle()
	{
		return $this->channel[ 'title' ];
	}


	/**
	 * Set the 'link' channel element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String  value of 'link' channel tag.
	 * 					Define the feed URL.
	 * @return  void
	 */
	public function setLink( $el=NULL )
	{
		$elNane = 'link';
		if ( self::controlChannelElements( $elNane, $el ) )
		{
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );

			if ( !isset( $this->channel[ 'id' ] ) || FeedValidator::isNull( $this->channel[ 'id' ] ) )
				$this->setId( $el );
		}
		else throw new Exception( "Error: '< channel >< $elNane>' XML element is mandatory and can not be empty, it must be a valid URL that specified the location of this feed." );
	}
	public function getLink()
	{
		return $this->channel[ 'link' ];
	}


	/**
	 * Set the 'id' channel element
	 *
	 * @access   public
	 * @param    String  value of 'id' channel tag
	 * @return   void
	 */
	public function setId( $el=NULL )
	{
		$elNane = 'id';

		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, $this->uuid( $el, 'ESSID:' ) );

		else throw new Exception( "Error: '< channel >< $elNane>' XML element is mandatory and can not be empty." );
	}
	public function getId()
	{
		return $this->channel[ 'id' ];
	}


	/**
	 * Set the 'generator' channel element
	 *
	 * @access   public
	 * @param    String  value of 'generator' channel tag
	 * @return   void
	 */
	public function setGenerator( $el=NULL )
	{
		$elNane = 'generator';

		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );

		else throw new Exception( "Error: '< channel >< $elNane>' XML element, if it is defined, can not be empty." );
	}
	public function getGenerator()
	{
		return $this->channel[ 'generator' ];
	}


	/**
	 * Set the 'published' channel element
	 *
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String  Value of 'published' channel tag.
	 * 					Must be an UTC Date format (ISO 8601).
	 * 					e.g. 2013-10-31T15:30:59+02:00 in Paris or 2013-10-31T15:30:59-08:00 in San Francisco
	 *
	 * @return  void
	 */
	public function setPublished( $el='now' )
	{
		$elNane = 'published';

		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, FeedWriter::getISODate( $el ) );

		else throw new Exception( "Error: '< channel >< $elNane>' XML element is mandatory and can not be empty." );
	}
	public function getPublished()
	{
		return $this->channel[ 'published' ];
	}


	/**
	 * Set the 'updated' channel element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param 	String  Value of 'updated' channel tag.
	 * 					Must be an UTC Date format (ISO 8601).
	 * 					e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco
	 * @return  void
	 */
	public function setUpdated( $el='now' )
	{
		$elNane = 'updated';

		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( 'updated', FeedWriter::getISODate( $el ) );

		else throw new Exception( "Error: '< channel >< $elNane>' XML element, if it is defined, can not be empty." );
	}
	public function getUpdated()
	{
		return $this->channel[ 'updated' ];
	}


	/**
	 * Set the 'rights' channel element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param 	String  value of 'rights' channel tag.
	 * 					Define the Feed proprietary rights.
	 * 					Should not be longer then 512 chars.
	 *
	 * @return void
	 */
	public function setRights( $el=NULL )
	{
		$elNane = 'rights';

		if ( self::controlChannelElements( $elNane, $el ) )
			$this->setChannelElement( $elNane, ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );

		else throw new Exception( "Error: '< channel >< $elNane>' XML element, if it is defined, can not be empty." );
	}
	public function getRights()
	{
		return $this->channel[ 'rights' ];
	}





  	/**
  	 * Generates an UUID
	 *
   	 * @author 	Anis uddin Ahmad <admin@ajaxray.com>
	 * @access	public
  	 * @param 	String  [OPTIONAL] String prefix
  	 * @return 	String  the formated uuid
  	 */
  	public static function uuid( $key=NULL, $prefix='ESSID:' )
	{
		$key = ( FeedValidator::isNull( $key ) )? uniqid( rand() ) : $key;
		$chars = md5( $key );
		$uuid  = substr( $chars, 0,8   ) . '-';
		$uuid .= substr( $chars, 8,4   ) . '-';
		$uuid .= substr( $chars, 12,4  ) . '-';
		$uuid .= substr( $chars, 16,4  ) . '-';
		$uuid .= substr( $chars, 20,12 );

		return $prefix . $uuid;
 	}

	/**
  	 * 	Generate or convert a String or an Integer parameter into an ISO 8601 Date format.
	 *
	 * 	@access 	public
	 * 	@param 		Object	date in seconds OR in convertible String Date (http://php.net/manual/en/function.strtotime.php)
	 *  					to convert in a ISO 8601 Date format: 'Y-m-d\TH:i:sZ'
	 * 	@return  	String
	 */
	public static function getISODate( $date=NULL )
	{
		if ( FeedValidator::isNull( $date ) == FALSE )
		{
			if ( strlen( $date ) >= 8 && !is_int( $date ) )
			{
				if ( FeedValidator::isValidDate( $date ) )
				{
					return $date;
				}
				else
				{
					return ( FeedValidator::isNull( strtotime( $date ) ) )?
						self::getISODate()
						:
						date( DateTime::ATOM, strtotime( $date )
					);
				}
			}
			else if ( intval( $date ) > 0 && FeedValidator::isOnlyNumsChars( $date ) )
				return date( DateTime::ATOM, $date );
		}
		else
		{
			$datetime_template = 'Y-m-d\TH:i:s';

			// control if PHP is configured with the same timezone then the server
			$timezone_server = exec( 'date +%:z' );
			$timezone_php	 = date( 'P' );

			if ( strlen( $timezone_server ) > 0 && $timezone_php != $timezone_server )
				return date( $datetime_template, exec( "date --date='@" . date( 'U' ) . "'" ) ) . $timezone_server;
			else
			{
				if ( date_default_timezone_get() == 'UTC' )
					 $offsetString = 'Z'; // No need to calculate offset, as default timezone is already UTC
				else
				{
				    $phpTime 		= date( $datetime_template );
				    $millis 		= strtotime( $phpTime ); 							// Convert time to milliseconds since 1970, using default timezone
				    $timezone 		= new DateTimeZone( date_default_timezone_get() ); 	// Get default system timezone to create a new DateTimeZone object
				    $offset 		= $timezone->getOffset( new DateTime( $phpTime ) ); // Offset in seconds to UTC
				    $offsetHours 	= round( abs( $offset ) / 3600 );
				    $offsetMinutes 	= round( ( abs( $offset ) - $offsetHours * 3600 ) / 60 );
				    $offsetString 	= ($offset < 0 ? '-' : '+' )
		                . ( $offsetHours < 10 ? '0' : '' ) . $offsetHours
		                . ':'
		                . ( $offsetMinutes < 10 ? '0' : '' ) . $offsetMinutes;
				}

				return date( $datetime_template, $millis ) . $offsetString;
			}
		}

		return addslashes( date( DateTime::ATOM, date( 'U' ) ) );
	}

	/**
	 * 	Extract images URL from a blog HTML content.
	 *
	 * @access	public
	 * @param	String	HTML content that can content fom <img /> XHTML element
	 * @return 	Array	Return an array of the images URL founds.
	 */
	public static function getMediaURLfromHTML( $text=NULL )
	{
		$media_ = array();

		if ( strlen( trim( $text ) ) > 0 )
		{
			$tt = preg_match_all( '/<(source|iframe|embed|param|img)[^>]+src=[\'"]([^\'"]+)[\'"].*>/i', str_replace( '><', '>
<', FeedValidator::removeBreaklines( $text,'
' ) ), $matches );

			if ( $tt > 0 && count( $matches[ 2 ] ) > 0 )
			{
				foreach ( $matches[ 2 ] as $i => $value )
				{
					$sb1 = array();
					$sb2 = array();
					$sb3 = array();
					$sb4 = array();
					if ( FeedValidator::isValidURL( $value ) )
					{
						$simple_tag = str_replace( "'","\"",strtolower( stripcslashes( $matches[ 0 ][ $i ] ) ) );

						$sb1 = explode( 'title="', $simple_tag );
						if ( count( $sb1 ) > 1 )
							$sb3 = explode( '"', $sb1[1] );

						$sb2 = explode( 'alt="', $simple_tag );
						if ( count( $sb2 ) > 1 )
							$sb4 = explode( '"', $sb2[1] );

						$media_type = FeedValidator::getMediaType( $value );

						array_push(
							$media_,
							array(
								'uri' 	=> $value,
								'type'	=> $media_type,
								'name'	=> ( ( isset( $sb3[ 0 ] ) )? $sb3[ 0 ] : ( ( isset( $sb4[ 0 ] ) )? $sb4[ 0 ] : $media_type . " - " . $i ) )
							)
						);
					}
				}
			}

			// Strip HTML content and analyzed individual world to find URL in the text. (CF: MediaWiki content).
			$text_split = explode( ' ', FeedValidator::getOnlyText( $text, self::CHARSET ) );

			if ( count( $text_split ) > 0 )
			{
				foreach ( $text_split as $value )
				{
					foreach ( array( 'image', 'sound', 'video' ) as $media_type )
					{
						if ( FeedValidator::isValidURL( $value ) )
						{
							if ( FeedValidator::isValidMediaURL( $value, $media_type ) )
							{
								if ( !in_array( $value, $media_ ) )
								{
									array_push( $media_, array(
										'uri' 	=> $value,
										'type'	=> $media_type,
										'name'	=> $media_type
									) );
								}
							}
						}
					}
				}
			}
		}
		return $media_;
	}



	private static function controlChannelElements( $elmName, $val=NULL  )
	{
		switch ( $elmName )
		{
			default	 		 : return ( FeedValidator::isNull( 		$val ) )? FALSE : TRUE;  break;
			case 'link' 	 : return ( FeedValidator::isValidURL( 	$val ) )? TRUE  : FALSE; break;
			case 'updated'	 :
			case 'published' : return ( FeedValidator::isValidDate( $val ) )? TRUE  : FeedValidator::isValidDate( self::getISODate( $val ) ); break;
		}
	}



	/**
	 * Prints the xml and ESS namespace
	 *
	 * @access   private
	 * @return   String
	 */
	private function getHead()
	{
		$out  = '<?xml version="1.0" encoding="'.self::CHARSET.'"?>' . self::LN;
		$out .= '<!DOCTYPE ess PUBLIC "-//ESS//DTD" "http://essfeed.org/history/'.urlencode( self::ESS_VERSION ).'/index.dtd">' . self::LN;
		$out .= $this->getComment();
		$out .= '<ess xmlns="http://essfeed.org/history/'.urlencode( self::ESS_VERSION ).'/" version="'. urlencode( self::ESS_VERSION ) .'" lang="'. $this->lang .'">' . self::LN;

		return $out;
	}

	private function getComment()
	{
		return '<!--' . self::LN .
			$this->t(1) . 'ESS Feed (Event Standard Syndication)' . self::LN .
			self::LN .
			$this->t(1) . 'Your events are now available to any software that read ESS format, example:' . self::LN .
			$this->t(2) . 'http://robby.ai             '.$this->t(1).' (AI Calendar Assistant)'  . self::LN .
			$this->t(2) . 'http://wp-events-plugin.com '.$this->t(1).' (Wordpress Event Plugin)' . self::LN .
			self::LN .
			$this->t(1) . 'Standard info:   '.$this->t(1).' http://essfeed.org/' . self::LN .
			$this->t(1) . 'Other libraries: '.$this->t(1).' https://github.com/essfeed/' . self::LN .
			self::LN .
		'-->' . self::LN;
	}

	/**
	 * Closes the open tags at the end of file
	 *
	 * @access   private
	 * @return   String
	 */
	private function getEndChannel()
	{
		return $this->t(1) . "</channel>" . self::LN . "</ess>". self::LN;
	}

	/**
	 * Creates a single node as xml format
	 *
	 * @access   private
	 * @param    String  name of the tag
	 * @param    Mixed   tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	 * @param    Array   Attributes(if any) in 'attrName' => 'attrValue' format
	 * @return   String  formatted XML tag
	 */
	private function makeNode( $tagName, $tagContent, $attributes = NULL )
	{
		$CDATA = array( 'description' ); // Names of the tags to be displayed with <[CDATA[...]]>.

		$nodeText = '';
		$attrText = '';

		if ( is_array( $attributes ) )
		{
			foreach ( $attributes as $key => $value )
			{
				if ( strlen( $value ) > 0 )
					$attrText .= " $key=\"$value\" ";
			}
		}

		$nodeText .= $this->t(2) . ( ( in_array( $tagName, $CDATA ) )? "<{$tagName}{$attrText}>" . self::LN . $this->t(3) . "<![CDATA[" . self::LN : "<{$tagName}{$attrText}>" );

		if ( is_array( $tagContent ) ) // for tags
		{
			{
				if ( isset( $value ) || $value == 0 )
				{
					$nodeText .= $this->t(4) . $this->makeNode( $key,
						( ( self::REPLACE_ACCENT )?
							FeedValidator::xml_entities(
								FeedValidator::noAccent( $value, self::CHARSET ),
								self::CHARSET
							)
							:
							$value
						)
					);
				}
			}
		}
		else
		{
			if ( in_array( $tagName, $CDATA ) ||
				 $tagName == 'published' ||
				 $tagName == 'updated' ||
				 $tagName == 'value' )			{ $nodeText .= self::utf8_for_xml( $tagContent ); }
			else if ( $tagName == 'start' ) 	{ $nodeText .= self::getISODate( $tagContent ); }
			else if ( $tagName == 'link' ||
					  $tagName == 'uri' )		{ $nodeText .= htmlspecialchars( $tagContent, ENT_QUOTES, self::CHARSET, FALSE ); }
			else
			{
				$nodeText .= FeedValidator::xml_entities(
					FeedValidator::noAccent( $tagContent, self::CHARSET ),
					self::CHARSET
				);
			}
		}

		$nodeText .= ( ( in_array( $tagName, $CDATA ) )? self::LN .  $this->t(3) . "]]>" . self::LN . $this->t(3) . "</$tagName>" : "</$tagName>" );

		return $nodeText . self::LN;
	}

    /**
     * convert unsuported UTF-8 chars within XML <[CDATA[...]]>.
     *
     * @access   private
     * @return   String
     */
    private static function utf8_for_xml($string)
    {
        if ( function_exists( 'mb_convert_encoding' ) )
        {
            $textORG = $string;
            $string = mb_convert_encoding( $string, self::CHARSET, "auto" );

            if ( strlen( $string ) <= 0 )
                $string = $textORG;
        }
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }

	/**
	 * Get Channel XML content in String format
	 *
	 * @access   private
	 * @return   String
	 */
	private function getChannel()
	{
		$out = $this->t(1) .'<channel>' . self::LN;

		foreach( $this->channel as $k => $v )
			$out .= $this->makeNode( $k, $v );

		return $out;
	}

	/**
	 * Get feed's items XML content in String format
	 *
	 * @access   private
	 * @return   String
	 */
	private function getItems()
	{
		$out = "";

		foreach ( $this->items as $item )
		{
			$thisRoots = $item->getRoots();
			$thisItems = $item->getElements();

			$out .= $this->startFeed();

			if ( is_array( $thisRoots ) )
			{
				if ( count( $thisRoots ) > 0 )
				{
					foreach ( $thisRoots as $elm => $val )
					{

						if ( $elm != 'tags' && is_string( $val ) )
						{
							if ( strlen( $elm ) > 0 && strlen( $val ) > 0 )
							{
								$out .= $this->t(1) .$this->makeNode( $elm, $val );
							}
						}
						else
						{
							if ( is_array( $val ) )
							{
								if  ( count( $val ) > 0 )
								{
									$out .= $this->t(3) . "<tags>" . self::LN;

									foreach( $val as $tag )
										$out .= $this->t(2) . $this->makeNode( 'tag', ( self::REPLACE_ACCENT )? FeedValidator::noAccent( $tag, self::CHARSET ) : $tag );

									$out .= $this->t(3) . "</tags>" . self::LN;
								}
							}
						}
					}
				}
			}

			if ( is_array( $thisItems ) )
			{
				if ( count( $thisItems ) > 0 )
				{
					foreach ( $thisItems as $key => $val )
					{
						if ( count( $thisItems[ $key ] ) > 0 && strlen( $key ) > 0 )
						{
							$out .= $this->t(3) . "<{$key}>" . self::LN;

							foreach ( $val as $position => $feedItem )
							{
								$out .= $this->t(4) . "<item type='". strtolower( $feedItem[ 'type' ] ) ."'".
									( ( isset( $feedItem[ 'unit' ]				) )? ( ( strlen( $feedItem[ 'unit' ] 				) > 0 )? " unit='".				strtolower( $feedItem[ 'unit' ]				) . "'" : '' ) : '' ) .
									( ( isset( $feedItem[ 'mode' ]				) )? ( ( strlen( $feedItem[ 'mode' ] 		 		) > 0 )? " mode='".				strtolower( $feedItem[ 'mode' ]				) . "'" : '' ) : '' ) .
									( ( isset( $feedItem[ 'selected_day' ]		) )? ( ( strlen( $feedItem[ 'selected_day' ] 		) > 0 )? " selected_day='".		strtolower( $feedItem[ 'selected_day' ]		) . "'" : '' ) : '' ) .
									( ( isset( $feedItem[ 'selected_week' ]		) )? ( ( strlen( $feedItem[ 'selected_week' ]		) > 0 )? " selected_week='".	strtolower( $feedItem[ 'selected_week' ]	) . "'" : '' ) : '' ) .
									( ( isset( $feedItem[ 'interval' ]			) )? ( ( intval( $feedItem[ 'interval' ]			) > 1 )? " interval='".			intval( $feedItem[ 'interval' ]				) . "'" : '' ) : '' )  .
									( ( isset( $feedItem[ 'limit' ]				) )? ( ( intval( $feedItem[ 'limit' ] 			) > 0 )? " limit='".			intval( $feedItem[ 'limit' ]				) . "'" : '' ) : '' )  .
									( ( isset( $feedItem[ 'moving_position' ]	) )? ( ( intval( $feedItem[ 'moving_position' ]	) > 0 )? " moving_position='".	intval( $feedItem[ 'moving_position' ]		) . "'" : '' ) : '' )  .
									( ( isset( $feedItem[ 'priority' ]			) )? ( ( intval( $feedItem[ 'priority' ]			) > 0 )? " priority='".			intval( $feedItem[ 'priority' ] 			) . "'" : " priority='".( $position + 1 ) . "'" )  : '' ) .
								">" . self::LN;

								if ( $key == 'prices' && ( $feedItem[ 'mode' ] == 'free' || $feedItem[ 'mode' ] == 'invitation' ) )
								{
									$out .= $this->t(3) . $this->makeNode( 'name', $feedItem['content']['name'] );
									$out .= $this->t(3) . $this->makeNode( 'value', 0 );
								}
								else
								{
									foreach ( $feedItem['content'] as $elm => $feedElm )
									{
										$out .= $this->t(3) . $this->makeNode( $elm, $feedElm );
									}
								}
								$out .= $this->t(4) . "</item>" . self::LN;
							}
							$out .= $this->t(3) . "</{$key}>" . self::LN;
						}
					}
				}
			}
			$out .= $this->endFeed();
		}
		return $out;
	}

	/**
	 * Create the starting tag of feed
	 *
	 * @access   private
	 * @return   String
	 */
	private function startFeed()
	{
		return $this->t(2) . '<feed>' . self::LN;
	}

	/**
	* Closes feed item tag
	*
	* @access   private
	* @return   String
	*/
	private function endFeed()
	{
		return $this->t(2) . '</feed>' . self::LN;
	}

	/**
	 *	Send email to server admin in case of specific problem.
	 *
	 * 	@access private
	 * 	@param	String	Receiver, or receivers of the mail.
	 * 	@param	String	Subject of the email to be sent.
	 * 	@param	String	Message to be sent in HTML format.
	 * 	@return Boolean	bool TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
	 */
	private static function sendEmail( $email=NULL, $subject=NULL, $message=NULL )
	{
		if ( isset( $email ) || isset( $subject ) || isset( $message ) && function_exists( 'email' ) )
		{
			$headers  = "From: " . 		strip_tags( $email ) . "\r\n";
			$headers .= "Reply-To: ". 	strip_tags( $email ) . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=" . self::CHARSET . "\r\n";

			$msg = "<html><body>";
			$msg .= $message;
			$msg .= '</body></html>';

			return mail( $email, $subject, $msg, $headers );
		}
		return FALSE;
	}

	public static function htmlvardump()
 	{
		ob_start();
		call_user_func_array( 'var_dump', func_get_args() );
		return ob_get_clean();
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
        static $tmp = NULL;

        if ( !$tmp )
        {
            $tmp = function_exists( 'sys_get_temp_dir' )? sys_get_temp_dir() : self::_tmp();
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
    protected static function _tmp()
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

	public static function get_geo()
	{
		// if the essfeed library is placed on a Google App Engine server
		if ( isset( $_SERVER[ 'HTTP_X_APPENGINE_CITYLATLONG' ] ) )
		{
			$ll_ = explode( ',', $_SERVER[ 'HTTP_X_APPENGINE_CITYLATLONG' ] );

			$_lat = $ll_[0];
			$_lng = $ll_[1];

			$city 			= @$_SERVER[ 'HTTP_X_APPENGINE_CITY' ];
			$country_code 	= @$_SERVER[ 'HTTP_X_APPENGINE_COUNTRY' ];
		}
		// if mod_geoip is installed. (http://dev.maxmind.com/geoip/mod_geoip2)
		else if ( isset( $_SERVER[ 'GEOIP_LATITUDE' ] ) ) // if mod_deoip installed on server
		{
			$_lat 			= $_SERVER[ 'GEOIP_LATITUDE' ];
			$_lng 			= $_SERVER[ 'GEOIP_LONGITUDE' ];
			$city 			= $_SERVER[ 'GEOIP_CITY' ];
			$country_code 	= $_SERVER[ 'GEOIP_COUNTRY_CODE' ];
		}
		else
		{
			$_lat         = 0;
			$_lng         = 0;
            $city         = "";
            $country_code = "";
		}
		return array(
			'lat' 			=> $_lat,
			'lng' 			=> $_lng,
			'city'			=> $city,
			'country_code' 	=> $country_code
		);
	}


	public static $AGGREGATOR_WS = "http://www.robby.ai/api/v1/ess/aggregator.json";
	public static $VALIDATOR_WS  = 'http://www.robby.ai/api/v1/ess/validator.json';

	public function pushToAggregators( $feedURL='', $feedData=NULL )
	{
		if ( $this->AUTO_PUSH )
		{
			$geo_ = self::get_geo();

			$post_data = array(
				'LIBRARY_VERSION'	=> self::LIBRARY_VERSION,
				'REMOTE_ADDR' 		=> ( ( isset( $_SERVER[ 'REMOTE_ADDR' ] 	) )? $_SERVER[ 'REMOTE_ADDR' ] 	: '' ),
				'SERVER_ADMIN'		=> ( ( isset( $_SERVER[ 'SERVER_PROTOCOL' ] ) )? $_SERVER[ 'SERVER_ADMIN' ] : '' ),
				'PROTOCOL'			=> ( ( isset( $_SERVER[ 'SERVER_PROTOCOL' ] ) )? ( ( stripos( $_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === TRUE )? 'https://' : 'http://' ) : 'http://' ),
				'HTTP_HOST'			=> ( ( isset( $_SERVER[ 'HTTP_HOST' ] 		) )? $_SERVER[ 'HTTP_HOST' ] 	: '' ),
				'REQUEST_URI'		=> ( ( isset( $_SERVER[ 'REQUEST_URI' ] 	) )? $_SERVER[ 'REQUEST_URI' ] 	: '' ),

				'GEOIP_LATITUDE' 	=> ( ( isset( $_SERVER[ 'lat' ] 			) )? $geo_['lat'] 				: '' ),
				'GEOIP_LONGITUDE'	=> ( ( isset( $_SERVER[ 'lng' ] 			) )? $geo_['lng'] 				: '' )
			);

			if ( $feedData == NULL && FeedValidator::isValidURL( $feedURL ) )
				$post_data[ 'feed' ] = $feedURL;
			else
				$post_data[ 'feed_file' ] = $feedData;

			// -- submit %_POST data with cURL
			$ch = @curl_init();

			if ( $ch != FALSE )
			{
				curl_setopt( $ch, CURLOPT_URL, 				self::$AGGREGATOR_WS );
				curl_setopt( $ch, CURLOPT_POSTFIELDS,  		$post_data );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 	1 );
				curl_setopt( $ch, CURLOPT_VERBOSE, 			1 );
				curl_setopt( $ch, CURLOPT_FAILONERROR, 		1 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 			20 );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 	0 );
				curl_setopt( $ch, CURLOPT_COOKIEJAR,  		self::tmp() . '/cookies' );
				curl_setopt( $ch, CURLOPT_REFERER, 			( ( isset( $_SERVER[ 'REQUEST_URI' ] ) )? $_SERVER[ 'REQUEST_URI' ] : '' ));

				$result = json_decode( curl_exec( $ch ), TRUE );
				$this->getAggregatorResponse( $result );

				//var_dump( $post_data, $result );
			}
			else
			{
				// -- submit %_POST data with file_get_contents()
				if ( ini_get( 'allow_url_fopen' ) )
				{
					$opts = array( 'http' => array(
							'method'  => 'POST',
							'header'  => "Content-Type: application/x-www-form-urlencoded",
							'content' => http_build_query( $post_data ),
							'timeout' => (60*20)
						)
					);

					$result = file_get_contents( self::$AGGREGATOR_WS, false, stream_context_create( $opts ), -1, 40000 );
					$this->getAggregatorResponse( $result );
				}
				else
				{
					// -- submit (only) %_GET data with "wget -q"
					if ( $feedData == NULL && FeedValidator::isValidURL( $feedURL ) )
					{
						$file = self::$AGGREGATOR_WS . "?";

						foreach ( $post_data as $att => $value )
							$file .= $att . "=" . urlencode( $value ) . "&";

						$result = exec( "wget -q \"" . $file . "\"" );
					}
				}
			}
		}
	}

	private function getAggregatorResponse( $response )
	{
		$r = $response['result'];
		$isOK = isset( $r['result'] )? TRUE : FALSE;
		$isVersionUptoDate = ( ( isset( $r['version'] ) )? ( (String) $r['version'] != (String)self::LIBRARY_VERSION && $isOK )? FALSE : TRUE : TRUE );

		if ( $this->DEBUG == TRUE )
		{
			$DARK_RED = '#ff0000;';

			$bg_color = ( $isOK )? '#91ff86' : '#ffd5d5';
			$mn_color = ( $isOK )? '#168c0a' : $DARK_RED;

			echo "<div style='background-color:$bg_color;color:$mn_color;border:1px solid $mn_color;width:95%;padding:10px;font-size:14px;margin:10px;'>".
				( ( $isVersionUptoDate == FALSE )?
					"<h3 style='color:$DARK_RED;font-size:20px;border:1px dotted $DARK_RED;padding:10px;margin:5px;'>The PHP-ESS library have been updated.<br/>".
						"Download the last GitHub version (".$r['version'].") : <a target='_blank' href='https://github.com/essfeed/php-ess'  style='color:#ff0000;'>https://github.com/essfeed/php-ess</a>".
					"</h3>" .
					"<br/>"
					:''
				).
				"Set the DEBUG attribute to false to remove this warning message.".
				"<br/><br/>".
				"$ newFeed = new FeedWriter();<br/>".
				"<b>$ newFeed->DEBUG = false;</b>".
				"<br/><br/>".
				self::htmlvardump( $response ) .
			"</div>";
		}

		if ( $isVersionUptoDate == FALSE && self::EMAIL_UP_TO_DATE && FeedValidator::isValidEmail( $_SERVER[ 'SERVER_ADMIN' ] ) )
		{
			self::sendEmail(
				$_SERVER[ 'SERVER_ADMIN' ],
				"Update your ESS Library on " . $_SERVER[ 'HTTP_HOST' ],
				"<h3>The library you used on your website ". $_SERVER[ 'HTTP_HOST' ] ." is not up to date</h3>".
				"<p style='background:#000;color:#FFF;padding:6px;'>".$_SERVER['DOCUMENT_ROOT'] . "</p>" .
				"You can upload the lastest version in <a href='https://github.com/essfeed/php-ess/'>https://github.com/essfeed/php-ess/</a>"
			);
		}
	}

	public static function error_submit( $error_blob )
	{
		if ( is_string( $error_blob ) )
		{
			if ( function_exists( 'mail' ) && strlen( $error_blob ) > 10 )
			{
				$protocol = ( ( isset( $_SERVER['HTTPS'] ) )? ( ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' )? 'https://' : 'http://' ) : 'http://' );

				$headers  = 'MIME-Version: 1.0' . "\r\n";
	    		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	    		$error_url = "<h4><a href='".$protocol . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."' target='_blank'>".$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."</a></h4>\n";
				mail('esserrorcontroller@peach.fr','### ESS-ERROR '.FeedWriter::LIBRARY_VERSION.' ###', $error_url . $error_blob, $headers );
			}
		}
	}

	public static function error_handler( $errno, $errstr, $errfile, $errline )
	{
		$err = "<b>ERROR ".$errno."</b>: ". $errstr ."<br/>\n" .
    	"<p>Error in " . $errfile .":". $errline ."</p><br/>\n" .
   		"<i>PHP " . PHP_VERSION . " (" . PHP_OS . ")</i><br/>\n";

		switch ( $errno )
		{
			case E_ERROR:
			case E_PARSE:
  	       		FeedWriter::error_submit(
					$err .
					FeedWriter::htmlvardump( $_SERVER  ) .
					"<br/>=================<br/>" .
					FeedWriter::htmlvardump( $_REQUEST )
				);
			break;
		}
	}
}