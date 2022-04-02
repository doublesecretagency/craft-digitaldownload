---
description: When automatically generating a link, it creates a new token each time the link is rendered. If you manually generate a token, store it to be used again later.
---

# Displaying a Link

:::warning Manual vs. Automatic Token Generation
When automatically generating a token, it'll create a new token every time the link is rendered. When you manually generate a token, you can store it to be used again later.

The main difference between generating a token manually or automatically is in **which parameters you provide to the function**.
:::

## Manual Token Generation

If you are generating a token separately, you can use it like this:

### url(token)

```twig
{% set token = craft.digitalDownload.createToken(asset, options) %}

<a href="{{ craft.digitalDownload.url(token) }}">{{ asset.title }}</a>
```

<hr style="margin:25px 0 18px">

When you bypass manual token creation, you can pass the Asset directly into the **url** or **link** methods. In these cases, you can also provide an [array of options](/creating-a-token/#createtoken-asset-options), which govern how and when the file can be accessed.

## Automatic Token Generation

If you are not generating a token separately, you can use it like this:

### url(asset, options = [])

```twig
<a href="{{ craft.digitalDownload.url(asset, options) }}">{{ asset.title }}</a>
```
