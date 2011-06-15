=== Warcraft News ===
Tags: widget,wow,feeds
Requires at least: 2.9.2
Tested up to: 3.1.3
Stable tag: 0.9.1.1

Shows news feeds of a World of Warcraft guild or World of Warcraft Character provided by the new battle.net armory in a widget, with caching.

== Description ==
Warcraft News provides a widget to display guild or character news from the new battle.net armory. You can add as many widgets as you want, e.g. one guild and three char news widgeds. It also can optionally include the wowhead javascript and replace the blizzard item links with links to wowhead with nice tooltips.

As a next step I plan to add a widget to show your current guild level and the newest guild perk.

This addon (currently) does not show a guild roster. There are other plugins around for that.

== Installation ==
Nothing fancy, just like any wordpress addon. After the installation make sure the cache directory (`wp-content/plugins/warcraft-news/cache`) is writable by wordpress.

If you don't use the automatic installer in the wordpress backend, try the following:

1. Upload and unzip the plugin to the `/wp-content/plugins/` directory
1. Make sure the cache directory (`wp-content/plugins/warcraft-news/cache`) is writable by wordpress.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Optionally configure the plugin in the settings tab


== Frequently Asked Questions ==

= There are typos in your plugin =
Possibly there are. But the whole content comes directly from battle.net. At the moment Blizzard seems to have had drunken translators, there are typos in the german battle.net, missing spaces and more. But if the typo isn't in the guilds news page but only in the plugin, please feel free to open a thread here on the wordpress site or contact me by mail.

= How can I change how the addon looks? =
ATM you'll need to edit your blogs stylesheet to change the look of the widgets. A good starting point might be the css class `li.widget_warcraft_news_guild ul`, but you might want to be more specific to your theme.
In the future I might include some color themes to chose from.


== Screenshots ==

1. The news of my guild on the guild blog.
2. News about my character Baraan on a standard wordpress installation.

== Changelog ==
= 0.9.2 =
Added a few classes and spans for more css possibilities
= 0.9.1 =
* Changed URL to the items on wowhead for the english lang
= 0.9 =
* First public release.

== Restrictions ==
* If you set the cache time to small, the armory might block you and the plugin won't work anymore. This is a Blizzard restriction I cannot circumvent.
* My guild uses a Umlaut in its name and I hope the plugin works well with all special chars around. If you have any problems, please send me a link to your guilds armory page and I try to work it out.
