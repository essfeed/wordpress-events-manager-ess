<?php
/**
 * ESS Feed DTD v0.9 (to check if tags exists and are mandatories)
 *
 * @package	ESSFeedWriter
 * @author  Brice Pissard
 * @link    http://essfeed.org/index.php/ESS_structure
 * @link	https://github.com/essfeed
 */
final class EssDTD
{
	// -- Event Privacity
	const ACCESS_PRIVATE	= 'PRIVATE';
	const ACCESS_PUBLIC		= 'PUBLIC';

	public function __construct() {}

	/**
	 *  Get ESS XML element attributes
	 *
	 * 	@access public
	 * 	@return Array	Return an Array of the <ess> XML element attributes with mandatory Boolean value DTD
	 */
	public static function getESSAttributeDTD()
	{
		return array(
			'xmlns' 	=> TRUE,
			'version'	=> TRUE,
			'lang'		=> TRUE
		);
	}

	/**
	 *  Get Channel's first available XML child
	 *
	 * 	@access public
	 * 	@return Array	Return an Array of the <channel> first XML childs with mandatory Boolean value DTD
	 */
	public static function getChannelDTD()
	{
		return array(
			'title'     	=> TRUE,
			'link'			=> TRUE,
			'id'			=> TRUE,
			'published'		=> TRUE,
			'updated'		=> FALSE,
			'generator'		=> FALSE,
			'rights'		=> FALSE,
			'feed'			=> TRUE
		);
	}

	/**
	 *  Get Feed first XML element DTD
	 *
	 * 	@access public
	 * 	@return Array	Return an Array of the DTD
	 */
	public static function getRootDTD()
	{
		return array(
			'title' 		=> TRUE,
		    'id'			=> TRUE,
		    'access'		=> TRUE,
		    'description'	=> TRUE,
		    'published'		=> TRUE,
		    'uri'			=> FALSE,
		    'updated'		=> FALSE,
		    'tags'			=> FALSE
		);
	}

	/**
	 *  Get Feed Complex XML child element DTD
	 *
	 * 	@access public
	 * 	@return Array	Return an Array of the DTD
	 */
	public static function getFeedDTD()
	{
		return array(
			'categories' => array(
				'mandatory' => TRUE,
				'types' 	=> array( 	'award',
										'competition',
										'commemoration',
										'conference',
										'concert',
										'cocktail',
										'course',
										'diner',
										'entertainment',
										'exhibition',
										'family',
										'friends',
										'festival',
										'lecture',
										'meeting',
										'networking',
										'party',
										'seminar',
										'trade show',
										'general'
									),
				'tags' 		=> array(
					'name' 			=> TRUE,
					'id' 			=> FALSE,
					'description'	=> FALSE
				)
			),
			'dates' => array(
				'mandatory'			=> TRUE,
				'types' 			=> array('standalone','recurrent','permanent'),
				'units'				=> array('hour','day','week','month','year'),
				'selected_days'		=> array("monday","tuesday","wednesday","thursday","friday","saturday","sunday"),
				'selected_weeks'	=> array("first","second","third","fourth","last"),
				'tags' 				=> array(
					'name' 			=> TRUE,
					'start' 		=> TRUE,
					'duration' 		=> FALSE,
					'description'	=> FALSE
				)
			),
			'places' => array(
				'mandatory' => TRUE,
				'types' 	=> array('fixed','area','moving','virtual'),
				'tags' 		=> array(
					'name' 			=> TRUE,
					'description'	=> FALSE,
					'country_code' 	=> FALSE,
					'country' 		=> FALSE,
					'latitude' 		=> FALSE,
					'longitude' 	=> FALSE,
					'address' 		=> FALSE,
					'city' 			=> FALSE,
					'zip' 			=> FALSE,
					'state' 		=> FALSE,
					'state_code'	=> FALSE,
					'medium_name' 	=> FALSE,
					'medium_type'	=> FALSE,
					'kml' 			=> FALSE
				)
			),
			'prices' => array(
				'mandatory' 		=> FALSE,
				'types' 			=> array('standalone','recurrent'),
				'modes'				=> array('fixed','free','donation','invitation','renumerated','prepaid'),
				'units'				=> array('hour','day','week','month','year'),
				'selected_days'		=> array("monday","tuesday","wednesday","thursday","friday","saturday","sunday"),
				'selected_weeks'	=> array("first","second","third","fourth","last"),
				'tags' 				=> array(
					'name' 			=> TRUE,
					'value' 		=> TRUE,
					'currency'		=> FALSE,
					'start' 		=> FALSE,
					'duration'		=> FALSE,
					'description'	=> FALSE,
					'quantity'		=> FALSE,
					'maximum'		=> FALSE,
					'minimum'		=> FALSE,
					'uri' 			=> FALSE
				)
			),
			'media' => array(
				'mandatory' => FALSE,
				'types' 	=> array('image','sound','video','website'),
				'tags' 		=> array(
					'name' 			=> TRUE,
					'uri' 			=> TRUE,
					'description'	=> FALSE
				)
			),
			'people' => array(
				'mandatory' => FALSE,
				'types' 	=> array('organizer','performer','attendee','social','author','contributor'),
				'tags' 		=> array(
					'name' 			=> TRUE,
					'id' 			=> FALSE,
					'firstname' 	=> FALSE,
					'lastname' 		=> FALSE,
					'organization' 	=> FALSE,
					'logo' 			=> FALSE,
					'icon' 			=> FALSE,
					'uri' 			=> FALSE,
					'address' 		=> FALSE,
					'city' 			=> FALSE,
					'zip' 			=> FALSE,
					'state' 		=> FALSE,
					'state_code'	=> FALSE,
					'country' 		=> FALSE,
					'country_code' 	=> FALSE,
					'email' 		=> FALSE,
					'phone' 		=> FALSE,
					'minpeople' 	=> FALSE,
					'maxpeople' 	=> FALSE,
					'minage' 		=> FALSE,
					'restriction'	=> FALSE,
					'description'	=> FALSE
				)
			),
			'relations' => array(
				'mandatory' => FALSE,
				'types' 	=> array('alternative','related','enclosure'),
				'tags' 		=> array(
					'name' 			=> TRUE,
					'uri'			=> TRUE,
					'id' 			=> TRUE,
					'description'	=> FALSE
				)
			),
		);
	}


}
