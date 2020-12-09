---
description: The best way to store a token for later use is to generate it using the Preparse field. This gives you the ability to re-use a single token repeatedly.
---

# Storing a Token

The best way to store a token for later use is to generate it using the [Preparse](https://plugins.craftcms.com/preparse-field) field. This gives you the ability to generate a single, specific token for an asset, and use it repeatedly on your site.

When creating a Preparse field, use something like this in the field configuration:

```twig
{%- spaceless -%}
    {% set myAsset = entry.myAsset.one() %}
    {% if myAsset %}
        {# Existing token, or generate new token #}
        {{ entry.myToken ?: craft.digitalDownload.createToken(myAsset) }}
    {% endif %}
{%- endspaceless -%}
```

For the purpose of the example above:

 - `myAsset` is an Assets field on the entry.
 - `myToken` is the self-referential handle for this Preparse field.

:::warning Replacing the Asset file
If you replace the file stored in your asset field, make sure to save it with the asset field **empty** first. This will clear the download token, so that it will generate a fresh one with a different asset file.
:::
