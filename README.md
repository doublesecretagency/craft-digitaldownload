Digital Download plugin for Craft CMS
=====================================

Provide secure digital download links to your files.

***

>This version is for Craft 3. To install it, visit the Plugin Store in your site's Control Panel.
>
>For the Craft 2 version, see [doublesecretagency.com/plugins/digital-download](https://www.doublesecretagency.com/plugins/digital-download)

***

## Display a Link

In its most basic form, you can generate a file download link with just a single line of code...

```twig
{{ craft.digitalDownload.link(file) }}
```

See the full docs on how to [display a link...](https://www.doublesecretagency.com/plugins/digital-download/docs/displaying-a-link)

## Create a Token

A token can be generated to denote a _specific_ file download with _specific_ expiration parameters. It's common practice to create a token, then store it for later use.

```twig
{% set token = craft.digitalDownload.createToken(file, options) %}
```

Learn which options are available when [creating a token...](https://www.doublesecretagency.com/plugins/digital-download/docs/creating-a-token)

## Store a Token

Here's a trick for automatically generating tokens. It uses the magic of a [Preparse field](https://github.com/aelvan/Preparse-Field-Craft) to store a persistent token in your Entry.

See how to [use this with your entries...](https://www.doublesecretagency.com/plugins/digital-download/docs/storing-a-token)

## Short Download Links

The file download link will mask the actual file location. You have an opportunity to [adjust the download link URL...](https://www.doublesecretagency.com/plugins/digital-download/docs/short-download-links)

***

## Anything else?

We've got other plugins too!

Check out the full catalog at [doublesecretagency.com/plugins](https://www.doublesecretagency.com/plugins)
