# Internet Archive Plugin for Craft CMS

Plugin for [Craft CMS](https://craftcms.com) that notifies the Internet Archive to archive entries on save.

## Installation

To install the plugin, follow these instructions:

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require matthiasott/craft-internetarchive

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for the Internet Archive plugin.

## Features

### Notify the Internet Archive of new and updated entries

The plugin pings the Internet Archive on every save of a published entry to archive the corresponding URL. It can then be accessed using the Internet Archive's [Wayback Machine](https://archive.org/web/). More information on how to trigger an archival and why this is a good idea can be found on the [IndieWeb Wiki](https://indieweb.org/Internet_Archive).

### Archive all live URLs at once

If you want to send the Internet Archive *all* URLs of your site at once, you can do so on the plugin’s control panel section.

## Changelog

### 1.0.0

* First version for Craft 5

## Roadmap

- Provide plugin settings to turn off saving URLs for certain entry types
- Archive all URLs of links within an entry, too

## Thank you!
Huge thanks to everyone involved in the Internet Archive project for saving our knowledge on the web and also to the [IndieWeb community](https://indieweb.org/), especially [Tantek Çelik](http://tantek.com).

## License

Code released under [the MIT license](https://github.com/matthiasott/internetarchive/LICENSE).

## Author

Matthias Ott    
<mail@matthiasott.com>    
<https://matthiasott.com>    