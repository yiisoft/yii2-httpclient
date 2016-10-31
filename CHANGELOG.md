Yii Framework 2 HTTP client extension Change Log
================================================

2.0.2 October 31, 2016
----------------------

- Bug #61: Response headers extraction at `StreamTransport` changed to use `$http_response_header` to be more reliable (klimov-paul)
- Bug #70: Fixed `Request::toString()` triggers `E_NOTICE` for not prepared request (klimov-paul)
- Bug #73: Fixed `Response::detectFormatByContent()` unable to detect URL-encoded format, if source content contains `|` symbol (klimov-paul)


2.0.1 August 04, 2016
---------------------

- Bug #44: Fixed exception name collision at `Response` and `Transport` (cebe)
- Bug #45: Fixed `XmlFormatter` unable to handle array with numeric keys (klimov-paul)
- Bug #53: Fixed `XmlParser` unable to parse value wrapped with 'CDATA' (DrDeath72)
- Bug #55: Fixed invalid display of Debug Toolbar summary block (rahimov)
- Enh #43: Events `EVENT_BEFORE_SEND` and `EVENT_AFTER_SEND` added to `Request` and `Client` (klimov-paul)
- Enh #46: Added `Request::getFullUrl()` allowing getting the full actual request URL (klimov-paul)
- Enh #47: Added `Message::addData()` allowing addition of the content data to already existing one (klimov-paul)
- Enh #50: Option 'protocolVersion' added to `Request::options` allowing specification of the HTTP protocol version (klimov-paul)
- Enh #58: Added `UrlEncodedFormatter::charset` allowing specification of content charset (klimov-paul)
- Enh: Added `XmlFormatter::useTraversableAsArray` allowing processing `\Traversable` as array (klimov-paul)


2.0.0.1 July 01, 2016
---------------------

- Enh: Fixed PHPdoc annotations (cebe)


2.0.0 July 1, 2016
------------------

- Initial release.
