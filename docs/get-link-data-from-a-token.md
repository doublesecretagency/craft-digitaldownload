---
description: Using an existing token, you can look up the parameters it was created with.
---

# Get Link Data from a Token

Using an existing token, you can look up the parameters it was created with.

:::code
```twig
{% set link = craft.digitalDownload.getLinkData(token) %}
```
```php
$link = DigitalDownload::$plugin->digitalDownload->getLinkData($token);
```
:::

A link contains the following properties:

 - `assetId`
 - `headers`
 - `enabled`
 - `expires`
 - `requireUser`
 - `maxDownloads`
 - `totalDownloads`
 - `lastDownloaded`
 - `dateCreated`

You may also take advantage of the following methods (in both Twig and PHP):

```twig
{# Get the related Asset for this link #}
{% set downloadableAsset = link.asset() %}

{# Get the download URL of this link #}
{% set downloadUrl = link.url() %}

{# Get a full <a> tag with the download link #}
{% set downloadLink = link.html("Download File") %}
```
