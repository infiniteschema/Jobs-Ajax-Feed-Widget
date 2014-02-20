=== Jobs Ajax Feed Widget ===
Contributors: Frettsy
Tags: rss, ajax, feed, jobs, indeed.com, indeed, indeed api
Requires at least: 2.0.2
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display job listings in an Ajax-powered RSS feed widget.

== Description ==

Display job listings in an Ajax-powered RSS feed widget. Uses Google AJAX Feed API. Based on Google AJAX Feed Widget by M Naveed Akram.

[__Indeed job listings RSS feed__](http://pipes.yahoo.com/pipes/pipe.info?_id=73cfb93650ba001eca64ccec9e892944)

=PARAMS=

q 	Query. By default terms are ANDed. ex: Accounting OR title:Accounting
l 	Location. Use a postal code or a "city, state/province/region" combination. ex: New York, NY
sort 	Sort by relevance or date. Default is date.
radius 	Distance from search location ("as the crow flies"). Default is 25.
jt 	Job type. Allowed values: "fulltime", "parttime", "contract", "internship", "temporary".
start 	Start results at this result number, beginning with 0. Default is 0.
limit 	Maximum number of results returned per query. Default is 10.
fromage 	Number of days back to search.
highlight 	Setting this value to 1 will bold terms in the snippet that are also present in q. Default is 0.
filter 	Filter duplicate results. 0 turns off duplicate job filtering. Default is 1.
co 	Search within country specified. Default is us. See here for a complete list of supported countries: http://snipt.org/zNK1
chnl 	Channel Name: Group API requests to a specific channel
publisher Publisher ID. This is assigned when you register as a publisher.
key	RSS feed access key.

== Installation ==

* Download zip from GitHub
* Upload through 'Plugins' menu in WordPress
* Activate

Also, if you’ve added custom user profile fields "pubid_indeed" and "pubkey_indeed", you can add something like the following to your functions.php:

define("META_KEY_PUBID_INDEED", "pubid_indeed");
define("META_KEY_PUBKEY_INDEED", "pubkey_indeed");


Look in the code for further info about using these fields.

== Frequently Asked Questions ==

== Screenshots ==

1. Settings preview

2. Widget preview

== Changelog ==

= 1.1 =
* Added required attribution link.
* Ability to use author_meta keys defined by META_KEY_PUBID_INDEED and META_KEY_PUBKEY_INDEED if widget-specific fields are left blank.

= 1.0 =
* First release.

== Upgrade Notice ==
