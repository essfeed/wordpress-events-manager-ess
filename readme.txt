=== Events Manager ESS ===
Contributors: essfeed
Donate link: http://essfeed.org
Tags: events, event, event registration, event calendar, events calendar, event management, events manager, feed, syndication, locations, maps, calendar,happenings, concerts, meetings, festivals,sport,diary,availability,feeds syndication, RSS, Atom, event publishers, XML, aggregator, crawling,crawler,google,indexation,high ranking,visibility,broadcast, schedule, SEO, booking, places, venue, upcoming, diary, ical, icalendar, organizer, planner,social events,social gatherings,cultural,museum,expo,cinema,movies,concert,jazz,festival,exhibition
Requires at least: 3.2
Tested up to: 4.0
Stable tag: 1.2
License: GPLv2 or later

Extends Events Manager Wordpress plugin to import, export and sync events through ESS feeds.

== Description ==
ESS for Event Standard Syndication is The Events Feed.
ESS is a free and open-source XML feed dedicated to describe events. This feed is read by search engines and robot crawlers to index your event pages and forward the web users to the your event URL.


**EVENTS MANAGER PLUGIN IS REQUIRED TO BE INSTALLED TO USE THIS PLUGIN**
[Events Manager Plugin](http://wordpress.org/plugins/events-manager/)


This plugin add several crucial SEO optimisations to **Events Manager** Plugin:

* ESS extension gives the ability to import and export ESS feeds
* ESS facilitate SEO and the event's broadcast.

* [ESS Documentation](http://essfeed.org)

[youtube http://www.youtube.com/watch?v=OGi0U3Eqs6E]

= Main Features =

* Import single event throught ESS Feed format
* Syndicate to 3rd party ESS feeds to automaticaly have the events updated.
* Export events into ESS format
* Auto submit events to Search Engines.

= Benefits of ESS =

* ESS increases user web traffic on event portal.
* ESS structures and organizes crucial event information to broadcast them efficiently.
* ESS prevents the duplication of content by centralizing event information in one website.
* ESS provide automatic transmition of events all over the web.
* ESS reduces the cost of event promotion thanks to centralization and automatization.
* With ESS, event information is not distorted or lost as it is by RSS and iCalendar feeds.



== Installation ==

Events Manager ESS Extension requiered Events Manager plugin to be installed, then it works like any standard Wordpress plugin, and requires little configuration. If you get stuck, visit the our documentation and support forums.
Whenever installing or upgrading any plugin, or even Wordpress itself, it is always recommended you back up your database first!

= Installing =

1. Go to Plugins > Add New in the admin area, and search for **Events Manager**.
  * [Events Manager](http://wordpress.org/plugins/events-manager/)
2. Click install, once installed, activate it.
3. Return to Plugins > Add New in the admin area, and search for **Events Manager ESS**.
  * [Events Manager ESS](http://wordpress.org/plugins/events-manager-ess/)
4. Click install, once installed, activate it, and that it!

Once installed, you can start adding events straight away and broadcat them to the world.

= Upgrading =

1. When upgrading, visit the plugins page in your admin area, scroll down to events manager and click upgrade.
2. Wordpress will help you upgrade automatically.

== Frequently Asked Question ==
You'll find the bug tracking in: [github.com issues](https://github.com/essfeed/wordpress-events-manager-ess/issues)


== Screenshots ==

1. Configure ESS Feeds settings
2. Configure ESS Feeds visibility
3. Definition of the event's organizer
4. Definition of the external events pages in relation with wordpress events
5. Section import or syndicate to 3rd party ESS feeds
6. ESS Feed rendered in the client browser


== Changelog ==

= 1.2 =
* add a "source link" at the end of the event description to get a back-link and improve the SEO
* prevent the duplication of category's name in the ESS feed

= 1.1 =
* fix the CRON auto update of syndicated feeds
* add a ESS to schema.org/Event binding to boost the google indexation
* update PHP 5.5 functions support
* set the ESS icon display as optional, ca be custom on the admin settings section.

= 0.98 =
* fix the duplicated 'exerpt' field in case of multiple events aggregation.
* update date calculation if the 'unit' attribute is provided within the important feed.
* update the category detection with the 'name' instead of the 'slug' because some languages have special chars that are url encoded.
* update the url encoding.
* improve the existing event detection in case of feed update.
* force country code transcription from 'xe' to 'gb' for england.
* leave HTML comments in the description of the ESS feed.
* set 'hour' as default value for every durations.

= 0.9 =
* handle PHP < 5.4 with ENT_XML1 native constant that doesn't exists
* prevent PHP E_STRICT and E_WARNINGS to be displayed on user screen.

= 0.8 =
* correct importation with multiple events in one feed
* correct some encoder in XTML and XML to be retrocompatible with php <= 5.3

= 0.5 =
* update the search engine push
* events automaticaly visible on https://www.hypecal.com/search

= 0.4 =
* combine GitHub repository with Worpress SVN
* add banner in Wordpress home page
* add FAQ in GitHub: https://github.com/essfeed/wordpress-events-manager-ess/issues

= 0.3 =
* add HTTP Request alternative to cURL to push events to search engines

= 0.2 =
* add screenshot images
* add plugin description

= 0.1 =
* submit first release