# UM Unique User Account ID
Extension to Ultimate Member for setting a prefixed Unique User Account ID per UM Registration Form.

## UM Settings
UM Settings -> Appearance -> Registration Form
1. Unique User Account ID - Form ID:prefix or meta_key format - Enter the UM Registration Form ID and the Unique User Account ID Prefix or meta_key format one setting per line.
2. Unique User Account ID - Number of digits - Enter the number of digits in the Unique User Account ID. Default value is 5.
3. Unique User Account ID - meta_key - Enter the meta_key name of the Unique User Account ID field. Default name is 'um_unique_account_id'

### Prefix format
<code>4840 : ABCD
4860 : EFGH-
4860 : Qwerty- : random
</code>

Numbers are based on WP 'user_id' field prefilled with zeros except when 'random' is specified.
1. Meta values for Registration Form ID 4840 and 5 digits: <code>ABCD00345</code> 
2. Meta values for Registration Form ID 4850 and 5 digits: <code>EFGH-00345</code>
3. Meta values for Registration Form ID 4860 and 5 random digits: <code>Qwerty-73528</code>

### meta_key format
<code>4840 : meta_key : um-field-name
4850 : meta_key : um-field-name : - 
4860 : meta_key : um-field-name : - : random
4870 : meta_key : um-field-name : : random
</code>

The Registration form user entered value for the meta_key 'um-field-name' will be used as prefix. In these examples user entered 'CompanyName'.

Numbers are based on  WP 'user_id' field prefilled with zeros except when 'random' is specified.
1. Meta values for Registration Form ID 4840 and 6 digits: <code>CompanyName000456</code>
2. Meta values for Registration Form ID 4850 and 6 digits: <code>CompanyName-000456</code>
3. Meta values for Registration Form ID 4860 and 6 random digits: <code>CompanyName-834602</code>
4. Meta values for Registration Form ID 4870 and 6 random digits: <code>CompanyName246739</code>

## Updates
1. Version 2.0.0 Added support for meta_key format and random numbers.
2. Version 2.1.0 Code improvements
3. Version 2.2.0 Updated for UM 2.8.3

## Translations or Text changes
1. Use the "Say What?" plugin with text domain ultimate-member

## Installation
1. Download the zip file and install as a WP Plugin, activate the plugin.
