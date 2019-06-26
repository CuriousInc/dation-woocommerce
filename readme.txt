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

You can download the latest ZIP from
 [dation-woocommerce on CircleCI](https://circleci.com/gh/CuriousInc/workflows/dation-woocommerce/tree/master).
 See the `dation-woocommerce.zip` artifact at the latest build.

The ZIP file has to be uploaded to your wordpress installation.
In the wp-admin environment, go to Plugins > New Plugin and upload the ZIP file.

After activating the plugin 'Dation' will show up in the side-bar

== Changelog ==

= 1.1.2 =
* Sanitize Rijksregisternummer

= 1.1 =
* Create students in Dation from order

= 1.0 =
* Load courses from Dation as products