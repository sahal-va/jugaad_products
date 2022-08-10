### INTRODUCTION:

The Jugaad products module creates a content type "Product"

Product content type will be having fields for title, description, image and App Purchase Link.

The Jugaad products module enables to list the products with a QR Code image that links to app.

Just pass the full url in the field to get QR Image for the given url.

Instead of displaying the 'App Purchase Link' on the site on the product page, this module display the link as a QR code, such that the site visitors can quickly open the product on the Jugaad app on their mobile.

It provides a block which can be placed anywhere on site.

If it is placed on product page, it will display the QR code for 'App Purchase Link' of that particular product.


### ADDITIONAL FEATURES:
The Jugaad products module provides a field formatter for string type field.
Field formatter displays a QR code created from the text/url given inside 'Jugaar Qr Code field'
This is disabled under the Products content type's manage display section. If needed, you can enable and use field formatter instead of placing the block.


### Note: 
A field formatter would be the best suit for our requirement rather than a custom block since the QR code generator requires a value/url always. Eg: If we want to use the QR code in a listing view.



*********
### REQUIREMENTS:

Install endroid/qr-code package from packagist.org using:
```bash
composer require endroid/qr-code-bundle
```


### INSTALLATION:

> Install in like any other module [link](https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules).

