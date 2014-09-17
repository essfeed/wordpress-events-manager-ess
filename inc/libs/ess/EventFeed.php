<?php
 /**
   * Universal ESS EventFeed Entry Writer
   * FeedItem class - Used as feed element in FeedWriter class
   *
   * @package 	ESSFeedWriter
   * @author  	Brice Pissard
   * @copyright No copyright
   * @license   GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
   * @link		http://essfeed.org/index.php/ESS_structure
   * @link		https://github.com/essfeed
   */
final class EventFeed
{
	private $CHARSET		= 'UTF-8';
	private $REPLACE_ACCENT = FALSE;
	private $roots 			= array();
	private $elements 		= array();
	private $rootDTD		= array();
	private $feedDTD		= array();

	public $errors_ 		= array();

	/**
	 * 	@access	public
	 * 	@see	http://essfeed.org/index.php/ESS_structure
	 * 	@param	Array	Array of data that represent the first elements of the feed.
	 *
	 * 	@return void;
	 */
	function __construct( $data_=NULL, $CHARSET='UTF-8', $REPLACE_ACCENT=FALSE )
	{
		$this->CHARSET 			= $CHARSET; 			// Defines the document encoding Charset (Default UTF-8).
		$this->REPLACE_ACCENT 	= $REPLACE_ACCENT;		// Defines if the ASCI accent have to be remplaced (Default FALSE).
		$this->rootDTD 			= EssDTD::getRootDTD();
		$this->feedDTD 			= EssDTD::getFeedDTD();

		foreach ( $this->feedDTD as $key => $value )
		{
			$this->elements[ $key ]	= array();
		}

		if ( $data_ != NULL && count( $data_ ) > 0 )
		{
			foreach ( $this->rootDTD as $elementName => $mandatory )
			{
				if ( $mandatory == TRUE && strlen( $data_[ $elementName ] ) <= 0 )
					throw new Exception("Error: Event element ". $elementName . " is mandatory.", 1);
			}

			foreach ( $data_ as $tagTest => $value )
			{
				$isFound = FALSE;

				if ( in_array( strtolower( $tagTest ), $this->rootDTD ) )
					$isFound = TRUE;

				if ( $isFound == FALSE )
					throw new Exception("Error: Event XML element < ". $tagTest . " > is not specified in ESS Feed DTD." );
			}

			foreach ( $data_ as $tag => $value )
			{
				if ( $tag != 'tag' )
				{
					$this->roots[ $tag ] = $value;
				}
				else
				{
					if ( is_array( $value ) )
					{
						$this->roots[ $tag ] = $value;
					}
					else throw new Exception("Error: Element < tag > must be of 'Array' type." );
				}
			}
		}
	}

	/**
	 * Set a Feed element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 * @param 	String  name of the feed tag.
	 * @param 	String  content of the feed tag.
	 * @return 	void
	 */
	private function setRootElement( $elementName, $content )
	{
		$CDATA = array('description');

		if ( is_string( $content ) && !in_array( $elementName, $CDATA ) )
			$content = FeedValidator::xml_entities( $content, $this->CHARSET );

		$this->roots[ $elementName ] = $content ;
	}



	// Root wrapper functions -------------------------------------------------------------------

	/**
	 * Set the 'title' feed element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String  value of 'title' feed tag.
	 * 					Define the language-sensitive feed title.
	 * 					Should not be longer then 128 characters
	 *
	 * @return  void
	 */
	public function setTitle( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'title', $el ) == FALSE )
			{
				throw new Exception( "Error: '< title >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'title', ( $this->REPLACE_ACCENT )? FeedValidator::noAccent( $el, $this->CHARSET ) : $el );

			// Set a tempory Feed ID from the title
			if ( !isset( $this->roots[ 'id' ] ) || FeedValidator::isNull( $this->roots[ 'id' ] ) )
			{
				$this->setId( $el );
			}
		}
	}
	public function getTitle()
	{
		return $this->roots[ 'title' ];
	}


	/**
	 * Set the 'uri' feed element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String  value of 'uri' feed tag.
	 * 			The URL have to be formated under RFC 3986 format, an IP can also be submited as a URL.
	 *
	 * @return 	void
	 */
	public function setUri( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'uri', htmlspecialchars( $el, ENT_NOQUOTES ) ) == FALSE )
			{
				throw new Exception( "Error: '< uri >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'uri', $el );

			// Set a tempory Feed ID from the Feed URI
			if ( !isset( $this->roots[ 'id' ] ) || FeedValidator::isNull( $this->roots[ 'id' ] ) )
			{
				$this->setId( $el );
			}
		}
	}
	public function getUri()
	{
		return $this->roots[ 'uri' ];
	}



	/**
	 * Set the 'id' feed element
	 *
	 * @access	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param 	String  value of 'id' feed tag
	 *
	 * @return 	void
	 */
	public function setId( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'id', $el ) == FALSE )
			{
				throw new Exception( "Error: '< id >' element is mandatory." );
				return;
			}
			$this->setRootElement( 'id', FeedWriter::uuid( $el, 'EVENTID:' ) );
		}
	}
	public function getId()
	{
		return $this->roots[ 'id' ];
	}


	/**
	 * Set the 'published' feed element
	 *
	 * @access	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param 	String  value of 'published' feed tag
	 * 			Must be an UTC Date format (ISO 8601).
	 * 			e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco
	 *
	 * @return 	void
	 */
	public function setPublished( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'published', $el ) == FALSE )
			{
				throw new Exception( "Error: '< published >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'published', FeedWriter::getISODate( $el ) );
		}
	}
	public function getPublished()
	{
		return $this->roots[ 'published' ];
	}


	/**
	 * Set the 'updated' feed element
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String  value of 'updated' feed tag
	 * 			Must be an UTC Date format (ISO 8601).
	 * 			e.g. 2013-10-31T15:30:59Z in Paris or 2013-10-31T15:30:59+0800 in San Francisco
	 *
	 * @return  void
	 */
	public function setUpdated( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'updated', $el ) == FALSE )
			{
				throw new Exception( "Error: '< updated >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'updated', FeedWriter::getISODate( $el ) );
		}
	}
	public function getUpdated()
	{
		return $this->roots[ 'updated' ];
	}


	/**
	 * Set the 'access' feed element
	 *
	 * @access 	public
	 * @see		http://essfeed.org/index.php/ESS_structure
	 *
	 * @param   String 	Define if the event have a public access.
	 * 					Can take the values: 'PUBLIC' or 'PRIVATE'
	 * @return 	void
	 */
	public function setAccess( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'access', $el ) == FALSE )
			{
				throw new Exception( "Error: '< access >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'access',
				( ( strtoupper( FeedValidator::charsetString( $el, $this->CHARSET ) ) === EssDTD::ACCESS_PRIVATE )?
					EssDTD::ACCESS_PRIVATE
					:
					EssDTD::ACCESS_PUBLIC
				)
			);
		}
	}
	public function getAccess()
	{
		return $this->roots[ 'access' ];
	}


	/**
	 * Set the 'description' feed element
	 *
	 * @access 	public
	 * @see 	http://essfeed.org/index.php/ESS_structure
	 *
	 * @param  	String  Event Feed description.
	 * 					This XML element contain the main text event description.
	 * 					ESS processors should use this content as main event description.
	 * 					Using HTML inside this section is not recommended because ESS processors could
	 * 					use this information in an environment that can not read HTML (car devices interface, iCal on mac...).
	 * @return 	void
	 */
	public function setDescription( $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'description', $el ) == FALSE )
			{
				throw new Exception( "Error: '< description >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'description', FeedValidator::stripSpecificHTMLtags( $el ) );
		}
	}
	public function getDescription()
	{
		return $this->roots[ 'description' ];
	}


	/**
	 * Set the 'tags' feed element
	 *
	 * @access 	public
	 * @see 	http://essfeed.org/index.php/ESS_structure
	 *
	 * @param  	Array 	Array of child elements with keywords content.
	 * 					ESS processors should use those keywords to specify the correct category that match with the event purpose.
	 *
	 * @return 	void
	 */
	public function setTags( Array $el=NULL )
	{
		if ( $el != NULL )
		{
			if ( $this->controlRoot( 'tags', $el ) == FALSE )
			{
				throw new Exception( "Error: '< tags >' element is mandatory." );
				return;
			}

			$this->setRootElement( 'tags', $el );
		}
	}
	public function getTags()
	{
		return $this->roots[ 'tags' ];
	}




	/**
	 * Add an element to elements array
	 *
	 * @access  private
	 * @param	String	Name of the group of tag.
	 * @param 	String  'type' attibute for this tag.
	 * @param 	String  'mode' attibute for this tag.
	 * @param 	String  'unit' attibute for this tag.
	 * @param	Array   Array of data for this tag element.
	 * @param 	String  'priority' attibute for this tag.
	 * @param 	String  'limit' attribute to restrict recurent type occuencies in Date or Price objects.
	 * @param 	String  'interval' attribute.
	 * @param 	String  'selected_day' attribute.
	 * @param 	String 	'selected_week' attribute.
	 * @return	void
	 */
	private function addElement(
		$groupName,
		$type 				= '',
		$mode 				= '',
		$unit 				= NULL,
		$data_ 				= NULL,
		$priority 			= 0,
		$limit				= 0, 	// only for <dates><item type="recurrent"> or <prices><item type="recurrent">
		$interval			= 1, 	// only for <dates><item type="recurrent"> or <prices><item type="recurrent">
		$selected_day		= NULL, // only for <dates><item type="recurrent"> or <prices><item type="recurrent">
		$selected_week		= NULL,	// only for <dates><item type="recurrent"> or <prices><item type="recurrent">
		$moving_position	= NULL  // only for <places><item type="moving">
	)
	{
		$groupName = strtolower( $groupName );

		$errorType = 'Error['.$groupName.']: ';

		if ( strlen( $type ) > 0 )
		{
			if ( count( $data_ ) > 0 )
			{
				if ( $this->controlType( $groupName, $type ) == TRUE )
				{
					if ( $this->controlMode( $groupName, $mode ) == TRUE )
					{
						if ( $this->controlSelectedDay( $groupName, $selected_day ) == TRUE )
						{
							if ( $this->controlSelectedWeek( $groupName, $selected_week ) == TRUE )
							{
								if ( $this->controlTags( $groupName, $data_ ) == TRUE )
								{
									foreach ( $data_ as $tag => $value )
									{
										if ( $this->controlNodeContent( $tag, $value ) == FALSE )
										{
											throw new Exception( $errorType . "The XML element < $tag > have an invalid content: '$value', please control the correct syntax in ESS DTD." );
											break;
										}
									}

									array_push(
										$this->elements[ $groupName ],
										array(
											'type' 				=> FeedValidator::charsetString( $type, 			$this->CHARSET ),
											'mode'				=> FeedValidator::charsetString( $mode,				$this->CHARSET ),
											'unit' 				=> FeedValidator::charsetString( $unit,				$this->CHARSET ),
											'priority'			=> FeedValidator::charsetString( $priority,			$this->CHARSET ),
											'limit'				=> FeedValidator::charsetString( $limit,			$this->CHARSET ),
											'interval'			=> FeedValidator::charsetString( $interval,			$this->CHARSET ),
											'selected_day'		=> FeedValidator::charsetString( $selected_day,		$this->CHARSET ),
											'selected_week'		=> FeedValidator::charsetString( $selected_week,	$this->CHARSET ),
											'moving_position'	=> FeedValidator::charsetString( $moving_position,	$this->CHARSET ),

											'content'			=> array_filter( array_unique( $data_ ) )
										)
									);
								}
								else
								{
									$mandatories = "";
									foreach ( $this->feedDTD[ $groupName ][ 'tags' ] as $tag => $mandatory )
									{
										if ( $mandatory == TRUE && strlen( $data_[ $tag ] ) <= 0 )
											$mandatories .= "< " .$tag." > ";
									}

									if ( FeedValidator::isNull( $mandatories ) == FALSE )
										throw new Exception( $errorType . "All the mandatories XML sub-elements of < $groupName > are not provided (".$mandatories.")." );
								}
							}
							else throw new Exception( $errorType . "Attribute selected_week='".$selected_week."' is not available in ESS DTD." );
						}
						else throw new Exception( $errorType . "Attribute selected_day='".$selected_day."' is not available in ESS DTD." );
					}
					else throw new Exception( $errorType . "Attribute mode='".$mode."' is not available in ESS DTD." );
				}
				else throw new Exception( $errorType . "Attribute type='".$type."' is not available in ESS DTD." );
			}
			else throw new Exception( $errorType . "Element could not be empty." );
		}
		else throw new Exception( $errorType . "The 'type' attribute is required." );
	}

	/**
	 * Return the collection of root elements in this feed item
	 *
	 * @access   public
	 * @return   Array
	 */
	public function getRoots()
	{
		return $this->roots;
	}

	/**
	 * Return the collection of elements in this feed item
	 *
	 * @access   public
	 * @return   Array
	 */
	public function getElements()
	{
		return $this->elements;
	}


	/**
	 * 	[MANDATORY] Add a Category to the current event feed.
	 * 				it is recommended add two categories per event feed for search engines to be more pertinents.
	 *
	 * @access  public
	 * @see 	http://essfeed.org/index.php/ESS:Categories
	 *
	 * @param	String	Define the purpose ot the event.
	 * 					Can take the values:
	 * 						'award',
	 * 						'commemoration',
	 * 						'competition',
	 * 						'conference',
	 * 						'concert',
	 * 						'diner',
	 * 						'entertainment',
	 * 						'cocktail',
	 * 						'course',
	 * 						'exhibition',
	 * 						'family',
	 * 						'friends',
	 * 						'festival',
	 * 						'lecture',
	 * 						'meeting',
	 * 						'networking',
	 * 						'party',
	 * 						'seminar',
	 * 						'trade show',
	 * 						'general'
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]					String 	Category name (Should not be longer then 128 chars)
	 * 						'id'			=> xxx,	// [OPTIONAL but RECOMMENDED] 	String 	Category ID (according to a specific taxonimy).
	 * 						'description'	=> xxx  // [OPTIONAL]					String 	Description of the event category (HTML tags accepted).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 *
	 * @return 	void
	 */
	public function addCategory(
		$type,
		$data_ 		= null,
		$priority	= 0
	)
	{
		 $this->addElement( 'categories', $type, null, null, $data_, $priority );
	}


	/**
	 * 	[MANDATORY] Add a Date for the event in the current event's feed.
	 *
	 * @access  public
	 * @see 	http://essfeed.org/index.php/ESS:Dates
	 *
	 * @param	String	Define the type of date of this event.
	 * 					Can take the values ("standalone", "permanent" or "recurrent").
	 * 					ESS Processors should consider that "standalone" is the default attribute if it is not specified.
	 *
	 * @param 	String	[OPTIONAL] 	The "unit" attribute only applied if type="recurrent" is specified.
	 * 								The "unit" attribute can take five values: "hour", "day", "week", "month" or "year".
	 * 								ESS processors should consider "hour" as the default "unit" attribute if it is not specified.
	 *
	 * @param 	int		[OPTIONAL] 	The "limit" attribute only applies if type="recurrent" is specified.
	 * 								The "limit" attribute is optional and defines the number of times the recurrent event will happen.
	 * 								If the "limit" attribute is not specified or if limits equal zero ESS Processors should consider the current event as infinite.
	 *
	 * @param	int		[OPTIONAL] 	The "interval" attribute only applies if type="recurrent" is specified.
	 * 								The "interval" attribute is optional and defines the number of time the recurrent event has to rescheduled the "unit" attribute to happen again.
	 * 								If the "interval" attribute is not specified ESS Processors should be consider the event with a interval="1".
	 *
	 * @param	String	[OPTIONAL] 	The "selected_day" attribute defines the type of "unit" attribute that has to be considered as repeated.
	 * 								The "selected_day" attribute can take eight types of values: "number", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" or "sunday".
	 * 								The "selected_day" attribute only applied if type="recurrent" is specified and if the unit="week" or unit="month".
	 *
	 * @param	String	[OPTIONAL] 	The "selected_week" attribute defines the section of the month that has to considered to be repeated.
	 * 								The "selected_week" attribute can take five types of values: "first", "second", "third", "fourth" or "last".
	 * 								The "selected_week" attribute only applies if type="recurrent" is specified and if the unit="month".
	 * 								If the "pselected_week" attribute is not specified ESS Processors should be considered the event as with a selected_week="".
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]					String 	date name (Should not be longer then 128 chars)
	 * 						'start'			=> xxx,	// [MANDATORY]					Date	date of the event under ISO 8601 format (e.g. 2013-10-31T15:30:59+0800 in Pasific Standard Time).
	 * 						'duration'		=> xxx	// [OPTIONAL but RECOMMENDED]  	Integer	duration in seconds (from start date).
	 * 						'description'	=> xxx  // [OPTIONAL]					String 	Description of the event date (HTML tags accepted).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addDate(
		$type			= "standalone",
		$unit			= "hour",
		$limit			= 0,
		$interval		= 1,
		$selected_day	= "number",
		$selected_week	= "first",
		$data_ 			= null,
		$priority		= 0
	)
	{
		 $this->addElement( 'dates', $type, null, $unit, $data_, $priority, $limit, $interval, $selected_day, $selected_week );
	}


	/**
	 * 	[MANDATORY] Add a Place to the current event feed.
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Places
	 *
	 * @param	String	Define the type of place of this event.
	 * 					Can take the values: "fixed", "area", "moving" or "virtual".
	 * 					ESS Processors should consider that "fixed" is the default attribute if it is not specified.
	 *
	 * @param	int		[OPTIONAL] 	Defines, only for events type="moving", the position of the event in the moving event.
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]					String 	location name (Should not be longer then 128 chars).
	 *						'country_code' 	=> xxx,	// [OPTIONAL but RECOMMENDED] sring 	2 chars country code (ISO 3166-1).
	 *						'country' 		=> xxx,	// [OPTIONAL but RECOMMENDED] 	String 	country name.
	 *						'latitude' 		=> xxx,	// [OPTIONAL but RECOMMENDED] 	Float 	number of the latitude of the event in Decimal Degrees: -90.XXXXXX to 90.XXXXXX (ISO 6709).
	 *						'longitude' 	=> xxx,	// [OPTIONAL but RECOMMENDED] 	Float 	number of the latitude of the event in Decimal Degrees: -180.XXXXXX to 180.XXXXXX (ISO 6709).
	 *						'address' 		=> xxx,	// [OPTIONAL but RECOMMENDED] 	String	event address.
	 *						'city' 			=> xxx,	// [OPTIONAL but RECOMMENDED] 	String	event city.
	 *						'zip' 			=> xxx,	// [OPTIONAL] 					String	event zip code.
	 *						'state' 		=> xxx,	// [OPTIONAL] 					String	event state.
	 *						'state_code'	=> xxx,	// [OPTIONAL] 					String	event state code.
	 *						'medium_name' 	=> xxx,	// [OPTIONAL] 					String	virtual event medium name. (only for type="virtual").
	 *						'medium_type'	=> xxx,	// [OPTIONAL] 					String	virtual event medium type ("television", "radio" or "internet").  (only for type="virtual").
	 *						'kml' 			=> xxx,	// [OPTIONAL] 					XML		area event surface representation. (only for type="area").
	 * 						'description'	=> xxx  // [OPTIONAL]					String 	Description of the event location or venue (HTML tags accepted).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addPlace(
		$type				= "fixed",
		$moving_position	= NULL,
		$data_ 				= NULL,
		$priority			= 0
	)
	{
		$this->addElement( 'places', $type, null, null, $data_, $priority, NULL, NULL, NULL, NULL, $moving_position );
	}


	/**
	 * 	[MANDATORY] Add a Price to the current event feed.
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Prices
	 *
	 * @param	String	Define the type of Price of this event.
	 * 					The "type" attribute can take two values: "standalone" or "recurrent".
	 * 					ESS Processors should consider that "standalone" is the default attribute if it is not specified.
	 *
	 * @param 	String	Reprensent the payment mode to assist to the event.
	 * 					The "mode" attribute can take four values: "fixed", "free", "invitation", "donation", "renumerated" or "prepaid".
	 * 					ESS Processors should consider that "fixed" is the default attribute if it is not specified.
	 *
	 * @param 	String	[OPTIONAL] 	The "unit" attribute only applied in type="recurrent" is specified.
	 * 								The "unit" attribute can take five values: "hour", "day", "week", "month" or "year".
	 * 								ESS processors should consider "hour" as the default "unit" attribute.
	 *
	 * @param 	int		[OPTIONAL] 	The "limit" attribute only applies if type="recurrent" is specified.
	 * 								The "limit" attribute is optional and defines the number of times the recurrent event will happen.
	 * 								If the "limit" attribute is not specified or if limits equal zero ESS Processors should consider
	 * 								the current event as infinite.
	 *
	 * @param	int		[OPTIONAL] 	The "interval" attribute only applies if type="recurrent" is specified.
	 * 								The "interval" attribute is optional and defines the number of time the recurrent event has to be rescheduled "unit" attribute to happen again.
	 * 								If the "interval" attribute is not specified ESS Processors should be consider the event with a selected="1".
	 *
	 * @param	String	[OPTIONAL] 	The "selected_day" attribute defines the type of "unit" attribute that has to be considered as repeated.
	 * 								The "selected_day" attribute can take eight types of values: "number", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" or "sunday".
	 * 								The "selected_day" attribute only applied if type="recurrent" is specified and if the unit="week" or unit="month".
	 *
	 * @param	String	[OPTIONAL] 	The "selected_week" attribute defines the section of the month that has to considered to be repeated.
	 * 								The "selected_week" attribute can take five types of values: "first", "second", "third", "fourth" or "last".
	 * 								The "selected_week" attribute only applies if type="recurrent" is specified and if the unit="month".
	 * 								If the "selected_week" attribute is not specified ESS Processors should be considered the event as with a selected_week="".
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]	String 	current price name (Should not be longer then 128 chars).
	 * 						'value' 		=> xxx,	// [MANDATORY]	Number 	current price value.
	 *						'currency'		=> xxx,	// [MANDATORY]	String 	current price 3chars currency (ISO 4217 format, e.g. USD, EUR...).
	 *						'start' 		=> xxx, // [OPTIONAL]	Date	date of the recurent billing under ISO 8601 format (only if type="recurent")
	 *						'duration'		=> xxx, // [OPTIONAL]	Integer	duration in seconds (from start date).
	 *						'uri' 			=> xxx  // [OPTIONAL]	URI		URL of the payment validation (invitation, webservice, paypal...) -  RFC 3986 format.
	 * 						'description'	=> xxx  // [OPTIONAL]	String 	Description of the event ticket (HTML tags accepted).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addPrice(
		$type			= "standalone",
		$mode			= "fixed",
		$unit			= "hour",
		$limit			= 0,
		$interval		= 1,
		$selected_day	= "number",
		$selected_week	= "first",
		$data_ 			= NULL,
		$priority		= 0
	)
	{
		 $this->addElement( 'prices', $type, $mode, $unit, $data_, $priority, $limit, $interval, $selected_day, $selected_week );
	}


	/**
	 * 	[OPTIONAL] Add a Person involve in the current event.
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:People
	 *
	 * @param	String	Define the type of persons involve in the event.
	 * 					Can take the values: "organizer", "performer", "attendee", "author", or "contributor".
	 * 					ESS Processors should consider that "organizer" is the default attribute if it is not specified.
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]					String 	current price name (Should not be longer then 128 chars).
	 * 						'id' 			=> xxx,	// [OPTIONAL]					URI		Unique and universal person identifier.
	 *						'firstname' 	=> xxx,	// [OPTIONAL]					String 	lirst name of the person.
	 *						'lastname' 		=> xxx,	// [OPTIONAL]					String 	last name of the person.
	 *						'organization' 	=> xxx,	// [OPTIONAL]					String 	organisation name (if applicable).
	 *						'logo' 			=> xxx,	// [OPTIONAL]					String 	URL of an image that identify the event actor (> 64px).
	 *						'icon' 			=> xxx,	// [OPTIONAL]					String 	URL of an icon that identify the event actor (<= 64px).
	 *						'uri' 			=> xxx,	// [OPTIONAL]					String 	URL of a page that describe the event actor.
	 *						'address' 		=> xxx,	// [OPTIONAL]					String 	address of the person.
	 *						'city' 			=> xxx,	// [OPTIONAL]					String 	city of the person.
	 *						'zip' 			=> xxx,	// [OPTIONAL]					String 	zip code of the person.
	 *						'state' 		=> xxx,	// [OPTIONAL]					String 	state code of the person.
	 *						'state_code'	=> xxx,	// [OPTIONAL]					String 	city of the person.
	 *						'country' 		=> xxx,	// [OPTIONAL]					String 	country name of the person.
	 *						'country_code' 	=> xxx,	// [OPTIONAL]					String 	country code in 2 chars of the person (ISO 3166).
	 *						'email' 		=> xxx,	// [OPTIONAL]					String 	email to contact the person.
	 *						'phone' 		=> xxx,	// [OPTIONAL]					String 	phone number to contact the person.
	 *						'minpeople' 	=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the minimum amount of attendees for this event. (only for type="attendee").
	 *						'maxpeople' 	=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the maximum amount of attendees for this event. (only for type="attendee").
	 *						'minage' 		=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the age minimum of attendees for this event. (only for type="attendee").
	 *						'restriction'	=> xxx,	// [OPTIONAL but RECOMMENDED]	String 	Defines the list of rules that the attendee should be aware of before attending the event. (only for type="attendee").
	 * 						'description'	=> xxx  // [OPTIONAL]					String 	Description of the person involved in the event (artist bio, performer summary, organizer description, (HTML tags accepted)).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addPeople(
		$type		= "organizer",
		$data_ 		= NULL,
		$priority	= 0
	)
	{
		 $this->addElement( 'people', $type, NULL, NULL, $data_, $priority );
	}


	/**
	 * 	[OPTIONAL] Add a Media file URL to the current event feed.
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Media
	 *
	 * @param	String	Define the type of Media file that represent the event.
	 * 					Can take the values: "image", "sound", "video" or "website".
	 * 					ESS Processors should consider that "image" is the default attribute if it is not specified.
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]	String 	name of the current media file. (Should not be longer then 128 chars).
	 * 						'uri' 			=> xxx,	// [MANDATORY]	URI 	current media file URL - under RFC 2396 format.
	 * 						'description'	=> xxx  // [OPTIONAL]	String 	Description of the media file (HTML tags accepted).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addMedia(
		$type		= "image",
		$data_ 		= NULL,
		$priority	= 0
	)
	{
		$this->addElement( 'media', $type, NULL, NULL, $data_, $priority );
	}

	/**
	 * 	[OPTIONAL] Add a Relation other events have with this current event feed.
	 *
	 * @access  public
	 * @see		http://essfeed.org/index.php/ESS:Relations
	 *
	 * @param	String	Define the type of event's relationanother event have with this event.
	 * 					Can take the values: "alternative", "related" or "enclosure".
	 * 					ESS Processors should consider that "alternative" is the default attribute if it is not specified.
	 *
	 * @param 	Array	Array of element to create the XML structure of the current tag where the index of the array represent the name of the tag.
	 * 					The structure the Array must be:
	 * 					array(
	 * 						'name' 			=> xxx,	// [MANDATORY]	String 	name of the other ess event in relation with the current one. (Should not be longer then 128 chars).
	 * 						'id' 			=> xxx,	// [MANDATORY]	URI 	unique and universal ESS feed (ess:id) identifier. Must be the same then the one defined the other ESS document.
	 * 						'uri' 			=> xxx,	// [MANDATORY]	URI 	define distant URI where is placed ESS Feed Document.
	 * 						'description'	=> xxx  // [OPTIONAL]	String 	Description of the event relation (HTML tags accepted).
	 * 					);
	 *
	 * @param 	int		[OPTIONAL] 	The "priority" attribute refers to the order and the preference applied to each <item> XML elements.
	 * 								ESS processors should consider the natural position of the <item> element as the priority if this attribute is not defined.
	 * @return 	void
	 */
	public function addRelation(
		$type		= "alternative",
		$data_ 		= NULL,
		$priority	= 0
	)
	{
		$this->addElement( 'relations', $type, NULL, NULL, $data_, $priority );
	}






	// -- Private Methods --

	private function controlRoot( $elmName, $val=null  )
	{
		foreach ( $this->rootDTD as $elm => $mandatory )
		{
			if ( strtolower( $elmName ) != 'tags' )
			{
				if ( $mandatory == TRUE && strlen( $val ) <= 0 )
					return FALSE;
			}
			else
			{
				if ( is_array( $val ) )
				{
					if ( count( $val ) <= 0 )
						return FALSE;
				}
				else FALSE;
			}
		}
		return TRUE;
	}

	private function controlTags( $elmName='', Array $data_=NULL )
	{
		foreach ( $this->feedDTD[ $elmName ][ 'tags' ] as $tag => $mandatory )
		{
			if ( $mandatory == TRUE )
			{
				if ( $tag == 'value' && intval( $data_[ $tag ] ) >= 0 )
					return TRUE;
				else if ( FeedValidator::isNull( $data_[ $tag ] ) )
					return FALSE;
			}
		}

		foreach ( $data_ as $tagTest => $value )
		{
			if ( FeedValidator::isNull( $value ) == FALSE )
			{
				if ( in_array( strtolower( $tagTest ), $this->feedDTD[ $elmName ][ 'tags' ] ) == FALSE )
					return FALSE;
			}
		}
		return TRUE;
	}

	private function controlType( $elmName='', $typeToControl='' )
	{
		if ( in_array( strtolower( $typeToControl ), $this->feedDTD[ $elmName ][ 'types' ] ) )
			return TRUE;
		return FALSE;
	}

	private function controlMode( $elmName='', $modeToControl='' )
	{
		if ( isset( $this->feedDTD[ $elmName ][ 'modes' ] ) )
		{
			if ( in_array( strtolower( $modeToControl ), $this->feedDTD[ $elmName ][ 'modes' ] ) )
				return TRUE;
		}
		else return TRUE;
		return FALSE;
	}

	private function controlSelectedDay( $elmName='', $selected_dayToControl='' )
	{
		if ( isset( $this->feedDTD[ $elmName ][ 'selected_days' ] ) && FeedValidator::isNull( $selected_dayToControl ) == FALSE )
		{
			$selected_ = explode( ',', $selected_dayToControl );

			if ( count( $selected_ ) > 0 )
			{
				foreach( $selected_ as $selected_dayToControl )
				{
					$elFound = FALSE;
					if ( in_array( strtolower( $selected_dayToControl ), $this->feedDTD[ $elmName ][ 'selected_days' ] ) )
						$elFound = TRUE;

					if ( $elFound == FALSE )
					{
						if ( intval( $selected_dayToControl ) <= 0 || intval( $selected_dayToControl ) > 31 )
							return FALSE;
						else $elFound = TRUE;
					}
				}
				return $elFound;
			}
			else
			{
				if ( in_array( strtolower( $selected_dayToControl ), $this->feedDTD[ $elmName ][ 'selected_days' ] ) )
					return TRUE;
			}
		}
		else return TRUE;
		return FALSE;
	}

	private function controlSelectedWeek( $elmName='', $selected_weekToControl='' )
	{
		if ( isset( $this->feedDTD[ $elmName ][ 'selected_weeks' ] ) && FeedValidator::isNull( $selected_weekToControl ) == FALSE  )
		{
			$selected_ = explode( ',', $selected_weekToControl );

			if ( count( $selected_ ) > 0 )
			{
				foreach( $selected_ as $selected_weekToControl )
				{
					if ( in_array( strtolower( $selected_weekToControl ), $this->feedDTD[ $elmName ][ 'selected_weeks' ] ) )
						return TRUE;
				}
			}
			else
			{
				if ( in_array( strtolower( $selected_weekToControl ), $this->feedDTD[ $elmName ][ 'selected_weeks' ] ) )
					return TRUE;
			}
		}
		else return TRUE;
		return FALSE;
	}

	private function controlNodeContent( $name, $value )
	{
		if ( FeedValidator::isNull( $value ) == FALSE )
		{
			switch ( strtolower( $name ) )
			{
				case 'start'			:
				case 'published' 		:
				case 'updated' 			: return FeedValidator::isValidDate( FeedWriter::getISODate( $value ) ); break;
				case 'name' 			: return ( FeedValidator::isNull( 			$value ) == FALSE )? TRUE : FALSE; break;
				case 'email' 			: return FeedValidator::isValidEmail( 		$value ); break;
				case 'logo' 			:
				case 'icon' 			:
				case 'uri' 				: return FeedValidator::isValidURL( 		$value ); break;
				case 'latitude'			: return FeedValidator::isValidLatitude(	$value ); break;
				case 'longitude'		: return FeedValidator::isValidLongitude(	$value ); break;
				case 'country_code' 	: return FeedValidator::isValidCountryCode( $value ); break;
				case 'currency' 		: return FeedValidator::isValidCurrency(	$value ); break;
				default					: return TRUE; break;
			}
		}
		return TRUE;
	}


 }