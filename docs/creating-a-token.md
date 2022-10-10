---
description: Where a link is automatically created, a new token will be created each time. To keep the same token for repeated use, create the token manually and store it.
---

# Creating a Token

:::warning Manual vs. Automatic
Tokens can be created manually (see below) or automatically, when [rendering a link on the fly](/displaying-a-link/).

In cases where a link is automatically created, **a new token will be created each time the link is rendered**. If you need to maintain the same token for repeated use, then you will want to create the token manually.
:::

Here's how to manually generate a token for your downloadable file...

:::code
```twig
{% set token = craft.digitalDownload.createToken(asset, options) %}
```
```php
$token = DigitalDownload::$plugin->digitalDownload->createToken($asset, $options);
```
:::

### Multiple Restrictions 

A token may have more than one limitation. If any condition evaluates as `true`, the file will be unavailable for download.

:::code
```twig
{# Limit to 1 year or 100 downloads (whichever comes first) #}
{% set token = craft.digitalDownload.createToken(asset, {
    'expires': 'P1Y',
    'maxDownloads': 100
}) %}
```
```php
// Limit to 1 year or 100 downloads (whichever comes first)
$token = DigitalDownload::$plugin->digitalDownload->createToken($asset, [
    'expires' => 'P1Y',
    'maxDownloads' => 100
]);
```
:::

## createToken(asset, options = [])

 - **asset** - The downloadable Asset.
 - **options** - A key-value set of options.

| Option       | Default  | Description
|:-------------|:---------|:------------------
| expires      | `'P14D'` | Time until asset key expires. Accepts any valid PHP interval specification.
| maxDownloads | `0`      | Maximum number of downloads allowed. (0 = unlimited)
| requireUser  | _null_   | If downloads are restricted to specific users and/or user groups.
| headers      | `{}`     | Associative array of optional HTTP headers to send during download.

The `requireUser` option can take on a variety of forms:

```php
null                      // Anyone can download (default)
'*'                       // Any logged-in user can download

492                       // Only one specific user (by user ID)
[492, 467]                // Multiple specific users

'myGroup'                 // Anyone in this user group
['mygroup', 'otherGroup'] // Multiple user groups

['mygroup', 467]          // Mix & match users and groups
```
