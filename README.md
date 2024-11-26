# UM Unique User Account ID
Extension to Ultimate Member for setting a prefixed Unique User Account ID per UM Registration Form.

## UM Settings -> Appearance -> Registration Form
1. Unique User Account ID - Form ID:prefix or meta_key or custom_keys format - Enter the UM Registration Form ID and the Unique User Account ID Prefix or meta_key format one setting per line.
2. Unique User Account ID - Number of digits - Enter the number of digits for the user ID. Default value is 5. Set to 1 for no prefilled zeros.
3. Unique User Account ID - Unique User Account ID meta_key - Enter the meta_key name of the Unique User Account ID field. Default name is 'um_unique_account_id'
4. Don't add a field to the Registration Form with this meta_key.

### Prefix format
<code>4840 : ABCD
4860 : EFGH-
4860 : Qwerty- : random
</code>

Number of digits are based on WP 'user_id' field prefilled with zeros except when 'random' is specified. Set Number of digits to 1 for no padded zeros.
1. Meta values for Registration Form ID 4840 and 5 digits: <code>ABCD00345</code> 
2. Meta values for Registration Form ID 4850 and 5 digits: <code>EFGH-00345</code>
3. Meta values for Registration Form ID 4860 and 5 random digits: <code>Qwerty-73528</code>

### meta_key format
<code>4840 : meta_key : um-field-name
4850 : meta_key : um-field-name : - 
4860 : meta_key : um-field-name : - : random
4870 : meta_key : um-field-name : : random
4880 : meta_key : um-field-name :-: permalink
</code>

The Registration form user entered value for the meta_key 'um-field-name' will be used as prefix. In these examples user entered 'CompanyName' except for the permalink option where user entered 'Company Name'.

Number of digits are based on  WP 'user_id' field prefilled with zeros except when 'random' is specified.
1. Meta values for Registration Form ID 4840 and 6 digits: <code>CompanyName000456</code>
2. Meta values for Registration Form ID 4850 and 6 digits: <code>CompanyName-000456</code>
3. Meta values for Registration Form ID 4860 and 6 random digits: <code>CompanyName-834602</code>
4. Meta values for Registration Form ID 4870 and 6 random digits: <code>CompanyName246739</code>
5. Meta values for Registration Form ID 4880 and Permalink format with a dash replacing blank and a suffix number from 2 for duplicates <code>company-name</code> and <code>company-name-2</code>

### custom_keys format
<code>4840 : custom_keys : - : meta_key1 : meta_key2 : meta_key3</code>

Number of digits are based on WP 'user_id' field prefilled with zeros up to the number of digits. Set Number of digits to 1 for no padded zeros.
1. Meta values for Registration Form ID 4840 with delimiter - and 6 digits: <code>value1-value2-value3-000456</code>

## Email placeholder
1. Use the UM email placeholder for the Unique User Account ID {usermeta:um_unique_account_id}
2. https://docs.ultimatemember.com/article/1340-placeholders-for-email-templates

## Permalink
1. UM Settings -> General -> Users
2. Profile Permalink Base -> Custom usermeta
3. Profile Permalink Base Custom Meta Key = 'um_unique_account_id'

## Translations or Text changes
1. Use the "Say What?" plugin with text domain ultimate-member

## References
1. Unique Membership ID - https://github.com/MissVeronica/um-unique-membership-id
2. Extra Custom Username Field - https://github.com/MissVeronica/um-custom-username-field

## Updates
1. Version 2.0.0 Added support for meta_key format and random numbers.
2. Version 2.1.0 Code improvements
3. Version 2.2.0 Updated for UM 2.8.3
4. Version 2.3.0 Addition of the Permalink option. Code improvements.
5. Version 2.3.1/2.3.2 Removal of non alpha/numeric characters in permalink mode.
6. Version 2.4.0 Addition of the "custom_keys" format option

## Installation & Updates
1. Install and update by downloading the plugin ZIP file via the green "Code" button
2. Install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.
3. Activate the Plugin: Ultimate Member - Unique User Account ID
