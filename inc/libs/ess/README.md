php-ess
=======

#### https://github.com/essfeed/php-ess

[![ESS Feed Standard](http://essfeed.org/images/8/87/ESS_logo_32x32.png)](http://essfeed.org/)

ESS: Event Standard Syndication.
ESS is free and open-source XML feed invented exclusively for events.
This library allows to generate ESS feeds with a simple instanciation and broadcast event's feeds to search engines.

To use this Class complete samples are available in /samples/complex_events.php :
- Simple fixed date.
- Recursive dates (several times a month, every last saturday for 6 months...)
- Free event or events with payment (with an payment API url).
- Event only available with invitations.
- Describe events with image, video and sounds.
...

## Usage
```PHP
/************************************************************************************************
 *
 *	This complete exemple create an event feed that describe:
 *
 *	- A 2 hours Madonna concert that happend every years for three years at 9:30PM the 25th of Oct.
 *  - It happends in a stadium in New York.
 * 	- This happening is defined with a category "concert" explained as "Rock music"
 *  - Several specific TAGs are attached to improve SEO.
 *  - The price is defined to $90 (with a URL for payment).
 * 	- The event can be free with a specific card accreditation.
 *	- An image and a video is defined to give a face to the event.
 *
 ************************************************************************************************/

include("FeedWriter.php");
$feed_url   = 'http://your_website.com/feeds/event-feed-123.ess';
$event_page = 'http://your_website.com/events/event-page-123.html';

// == Create the ESS Feed ================
$essFeed = new FeedWriter( 'en', array(
	'title'		=> 'Madonna events feed',
	'link'		=> $feed_url,
	'published'	=> '2013-10-25T15:30:00-08:00',
	'rights'	=> 'Madonna Copyright (c)'
));

	// == Create an Event =====================
	$newEvent = $essFeed->newEventFeed( array(
		'title'			=> 'Madonna Concert',
		'uri'			=> $event_page,
		'published'		=> 'now',
		'access'		=> 'PUBLIC',
		'description' 	=> "This is the description of the Madonna concert.",
		'tags'			=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )
	));
		// -- Define event's category(s) --------------------------------
		$newEvent->addCategory( 'concert', array(
			'name' => 'Rock Music'
		));

		// -- Define event's date(s) ------------------------------------
		$newEvent->addDate('recurrent', 'year', 3, null,null,null,array(
			'name'		=> 'Yearly concert',
			'start'		=> '2013-10-25T21:30:00Z',
			'duration'	=> '7200'
		));

		// -- Define event's place(s) -----------------------------------
		$newEvent->addPlace( 'fixed', null,array(
			'name'			=> 'Stadium',
			'latitude'		=> '40.71675',
			'longitude' 	=> '-74.00674',
			'address' 		=> 'Ave of Americas, 871',
			'city' 			=> 'New York',
			'zip' 			=> '10001',
			'state' 		=> 'New York',
			'state_code'	=> 'NY',
			'country' 		=> 'United States of America',
			'country_code' 	=> 'US'
		));

		// -- Define event's price(s) ------------------------------------------------------------
		$newEvent->addPrice('standalone','free',null,null,null,null,null,array('name'=>'ClubCard'));
		$newEvent->addPrice('standalone','fixed',null,null,null,null,null,array(
			'name'		=> 'Normal entrance',
			'value'		=> '90',
			'currency'	=> 'USD',
			'uri'		=> 'http://madonna.com/payment/api'
		));

		// -- Define event's social platform and people involved -------------------------------
		$newEvent->addPeople('performer',array('name'=>'Madonna' ) );
		$newEvent->addPeople('attendee',array('name'=>'Conditions','maxpeople'=>5000));
		$newEvent->addPeople('social',array('name'=>'Madona','uri'=>'http://facebook.com/madonna'));

		// -- Define event's media files (images, sounds, videos, websites) -------------------
		$newEvent->addMedia('image',array('name'=>'Photo 01','uri'=>'http://madonna.com/i.png'));
		$newEvent->addMedia('video',array('name'=>'Video 02','uri'=>'http://madonna.com/v.ogg'));

	// == Add the event to the Feed
	$essFeed->addItem( $newEvent );

	// == Other events can be created and added to the feed here...
	// ...


// == Display the ESS Feed generated.
$essFeed->genarateFeed();
```

The ESS XML results will be displayed for search engines and robot crawlers as:

https://github.com/essfeed/php-ess/blob/master/samples/simple_event.ess





## PHP Composer
The PHP library is available in [Composer](http://getcomposer.org/) for Symphony programmers in the [Packagist Repository](https://packagist.org/packages/essfeed/)
To install the PHP ESS Feed library, just add the following line in your "composer.json" file:
```PHP
{
	"require": {
    	...
    	"essfeed/event-feed": "dev-master"
    }
}
```




# Diference between RSS and ESS
Until now, event promoters could only use RSS or iCalendar to broadcast their events.
The problem has been that vital information gets lost in the event description.
Now, with ESS all the criteria of any events are clearly defined.

[![Publishing events with RSS](http://essfeed.org/images/6/64/Before_ess_with_rss.gif)](http://essfeed.org/)

[![Publishing events with ESS](http://essfeed.org/images/3/3b/After_with_ess.gif)](http://essfeed.org/)

[![Play the video](http://essfeed.org/images/e/ea/ESS-play-video.png)](http://www.youtube.com/watch?v=OGi0U3Eqs6E)


# Contributing

Contributions to the project are welcome. Feel free to fork and improve.
We accept pull requests and issues, especially when tests are included.

# License

(The MIT License)

Copyright (c) 2013

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
