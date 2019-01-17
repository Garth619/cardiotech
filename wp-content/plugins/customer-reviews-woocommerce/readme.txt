=== Customer Reviews for WooCommerce ===
Contributors: ivole
Tags: woocommerce, review plugin, review reminder, customer reviews, review for discount
Requires at least: 4.5
Tested up to: 5.0
Stable tag: 3.54
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html

Customer Reviews for WooCommerce plugin helps you get more sales with social proof. Set up automated review reminders and increase conversion rate.

== Description ==

Customer Reviews for WooCommerce plugin helps you get more sales with social proof. Encourage your customers leave product reviews and increase conversion of your shop. This WooCommerce review plugin enables you to set up automatic review reminders for customers who recently purchased a product from your shop. Reminder emails are sent to your customers inviting them to review the recent product(s) they purchased.

=== Features ===

Major features of Customer Reviews for WooCommerce include:

* Review reminder
* Aggregated review form
* Enhanced customer reviews
* Review for discount
* Import reviews

=== Review Reminder ===

Receive more authentic reviews from your real customers by sending automated invitations to submit a review. You will receive feedback from customers who never bother to answer surveys or submit reviews.

* Increase sales with social proof
* Receive reviews for several products at once by asking customers to answer a one-page review form
* Get great unique SEO content for your shop written by your customers
* Accept user-generated content (UGC) such as photos and videos uploaded by your customers along with reviews
* Send automated email invitations asking customers who recently purchased for a review
* Send email invitations manually for selected orders
* Personalize emails for each customer with built-in variables
* Restrict emails to particular categories of products
* Restrict emails to customers with particular user roles
* Works out of the box by using a responsive email template with custom colors
* Unsubscribe option
* Built-in testing tool to make sure that emails look beautifully before sending them
* Reminders in different languages via an integration with "qTranslate X" and "WPML" plugins

=== Aggregated Review Form ===

Let your customers review all the products from their orders on a single page.

* Automatically generate review forms for each WooCommerce order
* A single review form includes questions about all products in the order, so a customer will review several products at once
* After submission of the review form, the plugin will transfer reviews to pages of individual products
* Review forms are stored as static HTML files to ensure the fastest page load speed
* Review forms are optimized for different screen sizes (including mobile)
* Review forms include pictures of products
* Review forms support upload of photos and videos
* Review forms support general shop reviews (not specific to a particular product)
* Manual approval of reviews submitted via aggregated review forms

=== Enhanced Customer Reviews ===

Enhance the standard WooCommerce reviews with additional features.

* Enable customers to attach pictures to reviews
* Enhance rich snippets and structured data for reviews with pictures (requires WooCommerce 3.0 or newer)
* Prevent SPAM by enabling reCAPTCHA for reviews
* Show reviews summary bar on product pages
* Filter reviews by rating
* Enable visitors to vote on reviews left by your customers
* Built-in [cusrev_reviews] shortcode to display reviews inside post, page or widget. You can use this shortcode as [cusrev_reviews comment_file =”/comments.php”] or simply as [cusrev_reviews] on product pages. Here, 'comment_file' is an optional argument. If you have a custom comment file, you should specify it here.
* Built-in [cusrev_all_reviews] shortcode to display reviews on any page or post of a website. This shortcode supports arguments, e.g. [cusrev_all_reviews sort="DESC" per_page="10" number="-1" show_summary_bar="true" show_pictures="false" show_products="true" categories="" products=""]. Additional information about this shortcode is on the settings page of the plugin.

=== Review for Discount ===

Stimulate your customers to leave reviews and increase their lifetime value by offering discount codes. Send coupons to customers who reviewed their purchases to keep them engaged with your shop. It will help to increase repeat purchases and up-sells.

* Automatically generate new coupons for customers who review their purchases
* Automatically send emails with newly generated coupon or an existing coupon upon submission of a review
* Personalize emails with coupons for each customer using built-in variables
* Fine-tune properties of coupons according to your sales strategy
* Works out of the box by using a responsive email template with custom colors
* Built-in testing tool to make sure that emails look beautifully before sending them
* Emails with coupons in different languages via an integration with "qTranslate X" and "WPML" plugins

=== Trust Badges (Beta) ===

Increase your store’s conversion rate by placing a “trust badge” on the home, checkout or any other page(s). Let customers feel more confident about shopping on your site by featuring a trust badge that shows a summary of verified customer reviews.

=== Integration with Google Shopping (Beta) ===

Generate an XML feed with product reviews for Google Shopping and show star ratings in Google Shopping search results.

=== Import Reviews ===

Add product reviews from external websites to your shop using WooCommerce import reviews feature. This feature will automatically create reviews in WooCommerce based on a CSV file.

=== How does it work? ===

Customer Reviews for WooCommerce plugin works as follows:

1. A customer makes a purchase from your online shop
2. You process their order and set status of the order as “Completed”
3. After a certain delay (configured in the options of the plugin), the customer will receive an email with an invitation to leave a review
4. The customer writes reviews about products using a simple form
5. The customer receives an email containing a discount coupon for future purchases in your shop

=== Supported Languages ===

* Arabic
* Bulgarian
* Chinese
* Czech
* Danish
* Dutch
* English
* Estonian
* Finnish
* French
* German
* Hebrew
* Hungarian
* Indonesian
* Italian
* Japanese
* Korean
* Lithuanian
* Norwegian
* Polish
* Portuguese
* Romanian
* Russian
* Serbian
* Slovenian
* Spanish
* Swedish
* Thai
* Turkish
* Vietnamese
* You can contribute a translation to your language from the settings page of this plugin

=== Prerequisites ===

To use this plugin, you should first do the following:

1. Install and configure WooCommerce
2. Enable customer reviews for product pages (they are enabled by default for new installations)

=== Documentation ===

[Getting Started with Customer Reviews for WooCommerce](https://cusrev.freshdesk.com/support/solutions/articles/43000051291-getting-started-tutorial)

=== Premium Version ===

The plugin has a premium version that offers a possibility to white label the plugin and dedicated support by email. You can purchase a license for the premium version here: [Official Customer Reviews Plugin Website](https://www.cusrev.com).

== Installation ==

1. Make sure that WooCommerce plugin is installed and activated. If it is not installed, install [WooCommerce](https://wordpress.org/plugins/woocommerce/) first because it is necessary for this plugin.
2. Upload the plugin files to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the Reviews->Settings screen to configure the plugin

== Frequently Asked Questions ==

= I found a bug in the plugin. How to get it fixed? =

Please create a new topic at the support forum and provide detailed information with a screenshot.

= How to get Site Key and Secret Key for reCAPTCHA? =

Please visit [reCAPTCHA website](https://developers.google.com/recaptcha/docs/start) and sign up for an account. Then, you will be able to get API key pair (Site Key and Secret Key) for your website. Copy these keys and paste into the settings of the plugin.

= Will collected reviews be shown as stars in Google? =

The standard WooCommerce functionality already includes structured data mark up to display your product reviews effectively within organic search results. This plugin extends the standard functionality and adds some extra mark up to help search engines properly crawl your shop. It is important to understand that having a valid structured data mark up in place makes your website eligible for organic stars in Google but it doesn't guarantee that they will be shown. You can test the rich snippets using [Google’s Structured Data Testing Tool](https://search.google.com/structured-data/testing-tool).

= How to customize template of the reviews on a single product page? =
The plugin uses the standard reviews template provided by WooCommerce and enhances it with the reviews summary bar. If your theme requires a customized reviews template or you would like to use a customized reviews template for some other reason, it is possible to do so. Here is how to do it:
1) Create a folder 'customer-reviews-woocommerce' in your current theme's folder.
2) Copy file 'ivole-single-product-reviews.php' from 'templates' subfolder in the plugin's folder to the folder created at step 1.
3) Customize the file 'ivole-single-product-reviews.php' in the current theme's folder according to your requirements.

= How to Change Sorting of Reviews? =
If you would like to change how reviews are sorted on product pages, it is possible to do so using the standard WordPress functionality. Go to Settings -> Discussion menu and find options “Break comments into pages with X top level comments per page and the last/first page displayed by default” and “Comments should be displayed with the older/newer comments at the top of each page”. You can control how reviews are sorted and displayed on product pages by modifying these options.

== Screenshots ==

1. A sample email with an invitation to review products.
2. A sample review form generated by the plugin.
3. A sample email with a discount code.
4. The reviews summary bar.
5. Voting buttons for reviews.
6. A button to attach pictures to reviews.
7. reCAPTCHA field to prevent fake reviews.
8. A customer review with a picture.
9. [cusrev_all_reviews] shortcode to display reviews for all products on any page or post of a website
10. The first settings page.
11. The second settings page.
12. The third settings page.
13. The fourth settings page.
14. The fifth settings page.
15. The sixth settings page.
16. WooCommerce Orders page with manual reminders option enabled.
17. The diagnostic page.
18. Import reviews page.

== Changelog ==

= 3.54 =
* Bug fixes
= 3.53 =
* New feature: reply to reviews from WP admin area (an option to publish copies of replies to cusrev.com will be added in future versions)
* Bug fixes and minor improvements
= 3.52 =
* Bug fixes and minor improvements
= 3.51 =
* Bug fixes and minor improvements
= 3.50 =
* New feature: admin page with a list of scheduled review reminders
* Bug fixes and minor improvements
= 3.49 =
* Bug fixes and minor improvements
= 3.48 =
* New translation: Vietnamese
* Bug fixes and minor improvements
= 3.47 =
* New feature: integration with Google Shopping
* Bug fixes and CSS improvements
* Modifications requested by WordPress moderators
= 3.46 =
* New translation: Lithuanian
* Bug fixes
= 3.45 =
* New feature: shop rating (an option to include a separate question for a general shop review in addition to questions for product reviews)
* New feature: trust badges (visual badges to showcase a summary of your reviews)
* New feature: detailed error log for import of reviews
* New translation: Arabic
* New translation: Hebrew
= 3.44 =
* New translation: Korean
* Bug fixes related to WPML
= 3.43 =
* New translation: Turkish
= 3.42 =
* New translation: Japanese
= 3.41 =
* Bug fixes
= 3.40 =
* Bug fix: not possible to select product category on Review Discount tab
= 3.39 =
* New feature: detect staging sites and disable sending of automatic review reminders
* New feature: an option to restrict sending of review reminders for customers with specific user roles
* New feature: an option to disable display of lightboxes for pictures attached to reviews
* Bug fix: compatibility with PHP 5.4
= 3.38 =
* Bug fix: compatibility with PHP 5.4
= 3.37 =
* Bug fix: cannot use decimals in edit product
= 3.36 =
* New feature: plugin's settings are moved to a separate node in WordPress menu
* New feature: an admin page which shows all customer reviews
* New feature: additional arguments for 'cusrev_all_reviews' shortcode (products and categories of products)
* New feature: a new variable for review reminder templates to show a list of products in a customer order
= 3.35.3 =
* JS improvements for reCAPTCHA
= 3.35.2 =
* CSS improvements
= 3.35.1 =
* Bug fixes
= 3.35 =
* New feature: import reviews from CSV files
= 3.34 =
* Added a translation file for pt_BR
= 3.33 =
* Bug fixes
= 3.32 =
* New feature: add shop logo to email templates and review forms (requires the premium version)
* Bug fixes
= 3.31 =
* New feature: if customers have accounts on your website, send review reminders to emails associated with their accounts instead of their billing emails (optionally)
* Bug fixes
= 3.30 =
* New feature: WPML integration
* New feature: update of terms and conditions for GDPR
* Bug fixes
= 3.29 =
* New feature: always show 'Actions' column on WooCommerce Orders page when manual reminders are enabled
* New feature: show product names and icons when using [cusrev_all_reviews] shortcode
= 3.28 =
* New translation: Thai
= 3.27 =
* New feature: customers can upload pictures and videos on aggregated review forms (if this feature is enabled in the settings)
= 3.26 =
* New feature: show pictures uploaded with reviews when using [cusrev_all_reviews] shortcode
* Bug fixes
= 3.25.1 =
* Bug fixes
= 3.25 =
* New feature: a new screen to show a report about problems affecting the plugin
= 3.24 =
* New translation: Chinese
* Bug fixes
= 3.23 =
* Bug fixes
= 3.22 =
* New feature: pictures of products on aggregated review forms
* New feature: verified badges for reviews
* New feature: shortcode to display reviews on any page
* New translation: Norwegian
* Support for WordPress Multisite
= 3.21 =
* New translation: Bulgarian
* Modification: reCAPTCHA is shown below name and email fields for guests
* Bug fixes
= 3.20 =
* New feature: adjust size and optionally delete pictures uploaded by customers with their reviews. You can manipulate pictures in WordPress Media Library.
* New feature: the plugin will show replies to reviews when reviews are filtered by number of stars
* New feature: optionally limit number of review reminders sent per order to one
* New translation: Polish
* Bug fixes
= 3.19 =
* New feature: customize the maximum number of images uploaded per review
* New feature: customize the maximum size of an image uploaded with a review
* New feature: include tax in product prices on the aggregated review form according to the option "Display prices during cart and checkout" on "Tax" tab in WooCommerce settings
= 3.18.1 =
* Bug fixes: division by zero
= 3.18 =
* New translation: Estonian
* Extended translation of frontend user interface elements for existing languages
* Improvements in CSS
* New feature: support of custom templates (placed in the current theme's folder) for reviews section on single product pages
= 3.17 =
* New feature: voting for reviews, let visitors vote for reviews left by your customers
* Bug fixes
= 3.16 =
* New translation: Russian
= 3.15 =
* New feature: let customers control their privacy by choosing how their names are shown next to reviews
= 3.14 =
* New feature: added structured data mark up for reviews with images (associatedMedia, contentUrl)
= 3.13 =
* New feature: option to remove plugin's branding from the reviews summary bar on product pages (requires premium version)
* Important bug fixes
= 3.12 =
* Updated text domain
= 3.11 =
* New feature: customize shop name (previously, it was defaulted from WordPress Site Title)
* New translation: Indonesian
* New translation: Italian
* New translation: Romanian
= 3.10 =
* New feature: reviews summary bars for product pages
= 3.9 =
* New feature: if there are several variations of the same product in the order, the plugin will ask to review only the parent product
= 3.8.3 =
* New translation: Danish
* New translation: Serbian
= 3.8.2 =
* Bug fix: reCAPTCHA was required on admin pages when answering to reviews
* WordPress 4.9 compatibility
= 3.8 =
* New translation: Dutch
* New translation: Finnish
* New translation: Hungarian
* New translation: Slovenian
* Compatibility with W3 Total Cache plugin (DB caching)
* Additional check that Site Title is not empty when sending test emails
* New feature: modify "From" email address (requires premium version)
* New feature: modify "From" name (requires premium version)
* New feature: modify text in footer of emails (requires premium version)
* New feature: a dedicated tab for manually created coupons on the standard WooCommerce coupons page
= 3.7 =
* New translation: Czech
* New translation: French
* New translation: German
* New translation: Portuguese
* New translation: Spanish
= 3.6 =
* New feature: translate all parts of emails and review forms to your language
* New feature: option to make comments required when leaving a review
* Bug fix: number of stars wasn't properly updated after receiving a comment
= 3.5 =
* Resolves the problem with submission of reviews on sites without pretty permalinks
* New feature: integration with "qTranslate X" plugin for multilingual shops
= 3.4 =
* New feature: manual sending of review reminders
= 3.3 =
* More detailed reporting of errors when sending test emails
= 3.2 =
* New feature: customize colors of emails
* Bug fixes
= 3.1 =
* New feature: upload multiple images with a review
* New feature: images attached to reviews are opened in a lightbox
= 3.0 =
* Major update - you must check settings and test sending of emails immediately after updating the plugin
* New feature: responsive template for emails with review reminders and discount coupons
* New feature: sending of emails with review reminders and discount coupons is performed by Amazon SES to increase deliverability
* New feature: a customer will be able to review all products from their order on a single page using a stylish and lightweight form that loads blazingly fast
* New feature: specify reply-to address for emails with review reminders and discount coupons
* New feature: enable moderation of reviews submitted by customers in response to reminders
* New feature: a custom shortcode to display reviews inside post, page or widget
* Several email variables were discontinued
* Bug fixes
= 2.6 =
* Added support for ALTERNATE_WP_CRON
= 2.5 =
* Bug fixes and improvements
= 2.4 =
* Improvements in sending emails for old WooCommerce versions
= 2.3 =
* Additional improvements in compatibility with old WooCommerce versions
= 2.2 =
* Improved compatibility with old WooCommerce versions
= 2.1 =
* Bug fixes
= 2.0 =
* Added review for discount feature - automatically generate and send coupons to customers who left a review
= 1.5 =
* Added possibility to attach pictures to reviews
* Added support of reCAPTCHA to prevent fake reviews
* Updated settings page
= 1.4 =
* Bug fixes
= 1.3 =
* Bug fixes
= 1.2 =
* Bug fix for a missing file
= 1.1 =
* Bug fix for the error when WooCommerce is not installed
= 1.0 =
* Initial release.
