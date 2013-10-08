php-ess
=======

#### https://github.com/essfeed/php-ess

[![ESS Feed Standard](http://essfeed.org/images/8/87/ESS_logo_32x32.png)](http://essfeed.org/)

ESS (Event Standard Syndication) library in PHP
This classes allow to generate ESS feed with a simple instanciation.


To use this Class a complete example is available in /samples/complex_events.php
with more options and differents events types: 
- Simple fixed date.
- Recursive dates.
- Several times a month.
- Every last saturday for 6 months.
- Monthly billing payment (with an payment API url).
- Free event.
- Event only available with invitation.
- Motor race event moving to several places
- Images of the event only available in the HTML description: a simple script convert it into a feed entry.


## Usage

 	include("FeedWriter.php");
  	$new_feed_url  = 'http://example.com/feed/sample.ess';
  	$event_webpage = 'http://madonna.com/event/page.html'; 
  	
	// Create the ESS Feed
	$essFeed = new FeedWriter( 'en', array( 'title'=> 'ESS Feed','link'=> $new_feed_url,'published'=> '2013-10-25T15:30:00-08:00', 'rights'=> 'Copyright (c)'));
 	
 	// ####################################################################
 	// ###
	// ###  DEBUG  MODE  
	// ###	display on screen the result and explain the errors. 
	// ###	Have to be switch to false for production.
	// ###
  			$essFeed->DEBUG = true;
	// ####################################################################
 	
	// Create an Event (several methods can be called to assign various categories, prices, places,.. to the same event).
	$newEvent = $essFeed->newEventFeed( array( 'title'=> 'Madonna Concert', 'uri'=> $event_webpage, 'published'=> 'now', 'access'=> 'PUBLIC', 'description' => "This is the description of the Madonna concert.", 'tags'=> array( 'music', 'pop', '80s', 'Madonna', 'concert' )));
  		$newEvent->addCategory( 'concert', 											array('name'=> 'Rock Music', 'id'=> 'M22'));
		$newEvent->addDate( 	'recurrent', 'year', 1, null,null,null,				array('name'=> 'Yearly concert', 'start'=> '2013-10-25T15:30:00Z', 'duration'=> '7200' ) );
		$newEvent->addPlace( 	'fixed', null,										array('name'=> 'Stadium', 'latitude'=> '40.71675', 'longitude' => '-74.00674', 'address' => 'Ave of Americas, 871', 'city' => 'New York', 'zip' => '10001', 'state' => 'New York', 'state_code' => 'NY', 'country' => 'United States of America', 'country_code' => 'US' ) );
		$newEvent->addPrice(	'standalone', 'fixed', null,null,null,null,null,	array('name'=> 'Entrance with VIP access', 'value'=> '90', 'currency'=> 'USD', 'uri'=> 'http://madonna.com/payment/api'));
		$newEvent->addPeople(	'performer',										array('name'=> 'Madonna' ) );
		$newEvent->addMedia(	'image', 											array('name'=> 'Foto of Madonna', 'uri' => 'http://madonna.com/image.png'));					
		
	// Add the event to the Feed
	$essFeed->addItem( $newEvent );
	
	// Other event feed can be added... 
	
	// Display the ESS Feed generated On-The-Fly by PHP (consume PHP and DataBase resources at each robot access).
	$essFeed->genarateFeed();
	//$essFeed->genarateFeed('/var/local/www/site/feeds/events.ess');  // OR create the feed on server-side. Better to reduce the PHP and the Database access.

# Diference between RSS and ESS for events publication
[![Publishing events with RSS](http://essfeed.org/images/6/64/Before_ess_with_rss.gif)](http://essfeed.org/)
[![Publishing events with ESS](http://essfeed.org/images/3/3b/After_with_ess.gif)](http://essfeed.org/)


# Contributing

Contributions to the project are welcome. Feel free to fork and improve. I accept pull requests and issues,
especially when tests are included.

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
