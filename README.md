# DD_J_plg_content_iframe
is a Joomla! content plugin to add any iframe you like inside an article compliant with the EU Privacy Standard.

[![GPL Licence](https://badges.frapsoft.com/os/gpl/gpl.png?v=102)](https://opensource.org/licenses/GPL-2.0/)

This plugin allows to simple embed iframes like Facebook, Twitter, YouTube, etc... simply just any ifream you like inside articles, in the EU extended privacy mode.
There are options to define custom covers and parameters. By default it is setup for **EU Privacy**. This means a special two-click solution to embed the frame not before clicking to your cover. Recommended for Member States of the European Union as well as for other parties to the Agreement on the European Economic Area.
If you don't wish the EU Privacy mode, extended privacy can also be specified at plugin settings. The other settings can be setup for each iframe through our snipped parameters.

Please take note of the privacy policy of your country. We provide no liability for legal correctness!

For extended usage it is possible to define iframe parameter (width, heigth, class and framborder),
img parameter (width, heigth and class),
as well as any iframe HTML parameters like frameboarder, size, etc..
https://www.w3.org/TR/2011/WD-html5-20110525/the-iframe-element.html

# How to use
#### The simplest way,
insert this snipped into your article:

    {dd_iframe}src:XXXXXXXXXXX{/dd}

Replace ***XXXXXXXXXXX*** with your URL<br>

Or with a custom cover image

    {dd_iframe}src:XXXXXXXXXXX:cover:images/yourimagefile.jpg{/dd}

Replace ***images/yourimagefile.jpg*** width your cover image path<br>
(The relative cover image path to your image file at your website)

Note: The attribute value pairs must always be as follows:<br>
attribute:value:attribute:value<br>
Colon is assignment operator as well as separator.

Note: Enter src URL without Protokoll and without separtor!

Valid URL: src:www.iframe.hr-it-solutions.com
Invalid URL: src:https://www.iframe.hr-it-solutions.com

----

#### For extended usge,
you can define your insert snippet as follow:

An example with frameborder parameters:

    {dd_iframe}src:XXXXXXXXXXX:cover:images/yourimagefile.jpg:frameborder:0{/dd}

An example with iframe and img parameters:

    {dd_iframe}src:XXXXXXXXXXX:cover:images/yourimagefile.jpg:width:640:height:360:class:pull-right{/dd}

Parameters can also be combined. A Special usage has the cover image. You can also omit it from the plugin setting options to use a default cover from plugin setting options.

# System requirements
Joomla 3.x +                                                                                <br>
PHP 5.6.13 or newer is recommended.															<br>
PHP CURL (Only for the Iframe API feature, if enabled at Plugin settings)

# DD_ Namespace
DD_ is a namespace prefix, provided to avoid element name conflicts.						<br>

Author: HR-IT-Solutions Florian Häusler <info@hr-it-solutions.com> 							<br>
Copyright: (C) 2019 - 2019 HR-IT-Solutions GmbH 											<br>
License: GNU/GPLv2 only http://www.gnu.org/licenses/gpl-2.0.html