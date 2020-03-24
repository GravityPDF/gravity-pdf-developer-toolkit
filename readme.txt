=== Gravity PDF Developer Toolkit ===

== Frequently Asked Questions ==

= How do I receive support? =

User's with a valid, active license key can receive support for this plugin by filling out the form at [GravityPDF.com](https://gravitypdf.com/support/).

== Changelog ==

= 1.0.0-beta7, 24 March 2020 =
* Reset the page references when multiple PDFs are imported into a single template

= 1.0.0-beta6, 3 October 2019 =
* Fix permissions error PHP notice when importing PDFs
* Fix issue when RTL is enabled in the global PDF settings
* Update base template
* Fix multi-line font size issue

= 1.0.0-beta5, 29 November 2018 =
* Set the font-family when displaying tick icons in PDF
* Cast HTML to a string instead of throwing an exception and preventing the PDF generation

= 1.0.0-beta4, 5 September 2018 =
* Ensure the generated template filename won't contain a triple hyphen `---`
* Ensure mPDF object is either \mPDF or \Mpdf\Mpdf

= 1.0.0-beta3, 4 September 2018 =
* Add docblock for all variables available in generated templates
* Strip non ASCII characters and special characters from template name
* Skip the default font styles for legacy templates
* Prevent fatal error when the Writer hasn't been set for legacy templates

= 1.0.0-beta2, 16 July 2018 =
* Add better support for legacy templates
* Fix Javascript error when `gfpdf_current_pdf` object doesn't exist
* Swap annotation @method to @mixin to allow autocomplete in Toolkit templates
* When importing a PDF and determining the paper size, use core fonts only in mPDF
* Strip duplicate non-breaking space from the `$w->addMulti()` method
* Remove `hidden` option from `$w->add()` and `$w->addMulti()` as mPDF didn't function as expected
* Add new `$w->addCenter()`, `$w->addRight()`, `$w->addMultiCenter()` and `$w->addMultiRight()` methods for easier alignment

= 1.0.0-beta1, 18 April 2018 =
* Initial Release
