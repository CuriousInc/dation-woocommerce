=== Dation Woocommerce ===
Contributors: jvanhengeldationnl
Requires at least: 5.2
Tested up to: 5.2
Stable tag: trunk
Requires PHP: 7.2
License: GPLv3
License URI: https://raw.githubusercontent.com/CuriousInc/dation-woocommerce/master/LICENSE

Sell your Dation courses directly online!

== Description ==

Sell your Dation courses directly online! This [Wordpress](http://wordpress.org)
plugin pulls your courses from [Dation](https://dation.nl) and displays them in
your [Woocommerce](https://woocommerce.com) webshop (a Wordpress ecommerce plugin).

Students can enroll directly on your website and their details are uploaded to Dation automatically.

== Installation ==

Install and activate the plugin and go to the Dation Woocommerce settings page in the wp-admin menu. Fill in your driving school code and API-key to start fetching courses from Dation.
You can set prices, filter on (CCV) codes and enable specific 'Terugkommoment' features.

== Changelog ==
= 1.2.14 =
* prefix custom url parameters

= 1.2.13 =
* Add customization options for contact form

= 1.2.12 =
* add contact form directory to list of build directories

= 1.2.11 =
* add contact form page

= 1.2.10 =
* Fix after changing date formats

= 1.2.9 =
* Change date formats for dation Api

= 1.2.8 =
* Add lead contact form page. Page can be set in 'settings'. Will be added to extra field 'product_url'.

= 1.2.7 =
* Add new 'Reason for delay' option for TKM 2.

= 1.2.6 =
* Add new 'Reason for delay' option for TKM.

= 1.2.5 =
* Add location information to Dation Woocommerce Product, to be used in email confirmation.

= 1.2.4 =
* Add humanly formatted date at product attribute

= 1.2.2 =
* Add 'Reason for delay' for TKM moments that are later than 11 months after the issue date of the drivers license

= 1.2.0 =
* Generalize plugin. TKM options behind checkbox. Added filter for training(ccv) codes.

= 1.1.2 =
* Sanitize Rijksregisternummer

= 1.1 =
* Create students in Dation from order

= 1.0 =
* Load courses from Dation as products