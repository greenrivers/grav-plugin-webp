# Greenrivers Webp Plugin

The **Webp** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav).

It allows You to conversion images to webp extension.

## Installation

Installing the Webp plugin can be done in one of three ways:
- GPM (Grav Package Manager)
- manual method
- admin method

### GPM Installation (Preferred)

To install the plugin via the [GPM](http://learn.getgrav.org/advanced/grav-gpm), through your system's terminal (also called the command line),
navigate to the root of your Grav-installation, and enter:

    bin/gpm install webp

This will install the Webp plugin into your `/user/plugins`-directory within Grav. Its files can be found under `/your/site/grav/user/plugins/webp`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`.
Then rename the folder to `webp`. You can find these files on [GitHub](https://github.com/greenrivers/grav-plugin-webp) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/webp

> NOTE: This plugin is a modular component for Grav which may require other plugins to operate,
> please see its [blueprints.yaml-file on GitHub](https://github.com/greenrivers/grav-plugin-webp/blob/master/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Requirements

Make sure that You have installed and enabled webp support:

- install packages: libwebp-dev, webp
- enable GD and configure PHP to enable support for webp format

## Configuration

Before configuring this plugin, you should copy the `user/plugins/webp/webp.yaml` to `user/config/plugins/webp.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named webp.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

Click on **Convert** button from plugin settings in admin menu.

Info about number of the converted images should appear next to the **Convert** button.

Plugin supports following extensions:

- jpg
- jpeg
- png

<ins>After conversion You should clear cache.</ins>

To display converted image use **webp** filter in twig templates.

### Examples:

Raw image url.

```html
<img alt="webp image" src="{{ '/user/images/my-image.jpg'|webp }}">
```

Source from variable.

```html
<img alt="webp image" src="{{ url(logo)|webp }}">
```

### Issues:

Sometimes after plugin activation You can see text field instead of button & progressbar.<br/>
To resolve it, save config again with **Enabled** status.

![Plugin enabled bug](assets/images/plugin-enabled-bug.webp)

## Credits

https://www.php.net/manual/en/image.installation.php

https://developers.google.com/speed/webp

## To Do

- [ ] Create **Clear all** option
- [ ] Add console commands
- [ ] Add unit tests
