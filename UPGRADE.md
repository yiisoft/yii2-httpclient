Upgrading Instructions for Yii Framework v2 HTTP Client Extension
=================================================================

!!!IMPORTANT!!!

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to following the instructions
for both A and B.

Upgrade from yii2-httpclient 2.0.6
----------------------------------

* Classes `Request` and `Response` have been updated to match interfaces `Psr\Http\Message\RequestInterface`
  and `Psr\Http\Message\ResponseInterface` accordingly. Make sure you use their methods and properties correctly.

* Methods method `Message::getHeaders()` and corresponding virtual property `$headers` are no longer return `HeaderCollection`
  instance. Use corresponding methods from `Psr\Http\Message\MessageInterface` for headers manipulations.
  You can use `getHeaderCollection()` in order to use old headers setup syntax.

* Methods `getContent()`, `setContent()` and virtual property `$content` have been removed from `Message`.
  Use methods `getBody()`, `setBody()` and `withBody()` instead.

* Methods `getFullUrl()`, `setFullUrl()` and virtual property `$fullUrl` have been removed from `Request`.
  Use methods `getUri()`, `setUri()` and `withUri()` instead.

* Methods `getData()`, `setData()` and `addData()` as well as virtual property `$data` have been removed from `Request`.
  Use methods `getParams()`, `setParams()`, `addParams()` or virtual property `$params` instead.

* Methods `getData()`, `setData()` and `addData()` as well as virtual property `$data` have been removed from `Response`.
  Use methods `getParsedBody()`, `setParsedBody()` or virtual property `$parsedBody` instead.

* Method `addContent()` has been removed from `Request`. Use `addBodyPart()` instead.

* Signature of the method `Client::createRequestLogToken()` has been changed to accept `Request` instance as a sole argument.
  Make sure you invoke or override this method correctly.
