<img align="left" src="https://plugins.doublesecretagency.com/digital-download/images/icon.svg" alt="Plugin icon">

# Digital Download plugin for Craft CMS

**Provide secure digital download links to your files.**

---

**For complete documentation, see [plugins.doublesecretagency.com/digital-download](https://plugins.doublesecretagency.com/digital-download/)**

## Display a Link

In its most basic form, you can generate a file download link with just a single line of code...

```twig
{{ craft.digitalDownload.link(asset) }}
```

See the full docs on how to [display a link...](https://plugins.doublesecretagency.com/digital-download/displaying-a-link/)

## Create a Token

A token can be generated to denote a _specific_ file download with _specific_ expiration parameters. It's common practice to create a token, then store it for later use.

```twig
{% set token = craft.digitalDownload.createToken(asset, options) %}
```

Learn which options are available when [creating a token...](https://plugins.doublesecretagency.com/digital-download/creating-a-token/)

## Store a Token

Here's a trick for automatically generating tokens. It uses the magic of a [Preparse field](https://plugins.craftcms.com/preparse-field) to store a persistent token in your Entry.

See how to [use this with your entries...](https://plugins.doublesecretagency.com/digital-download/storing-a-token/)

## Short Download Links

The file download link will mask the actual file location. You have an opportunity to [adjust the download link URL...](https://plugins.doublesecretagency.com/digital-download/short-download-links/)

---

## Further Reading

If you haven't already, flip through the [complete plugin documentation](https://plugins.doublesecretagency.com/digital-download/).

And if you have any remaining questions, feel free to [reach out to us](https://www.doublesecretagency.com/contact) (via Discord is preferred).

**On behalf of Double Secret Agency, thanks for checking out our plugin!** 🍺

<p align="center">
    <img width="130" src="https://www.doublesecretagency.com/resources/images/dsa-transparent.png" alt="Logo for Double Secret Agency">
</p>
