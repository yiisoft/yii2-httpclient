Request Options
===============

You may use [[\yii\httpclient\Request::$options]] to adjust particular request execution.
Following options are supported (case-sensitive):
 - timeout: integer, the maximum number of seconds to allow request to be executed.
 - proxy: string, URI specifying address of proxy server. (e.g. tcp://proxy.example.com:5100).
 - userAgent: string, the contents of the "User-Agent: " header to be used in a HTTP request.
 - followLocation: boolean, whether to follow any "Location: " header that the server sends as part of the HTTP header.
 - maxRedirects: integer, the max number of redirects to follow.
 - sslVerifyPeer: boolean, whether verification of the peer's certificate should be performed.
 - sslCafile: string, location of Certificate Authority file on local filesystem which should be used with
   the 'sslVerifyPeer' option to authenticate the identity of the remote peer.
 - sslCapath: string, a directory that holds multiple CA certificates.
 - sslLocalCert: Path to local certificate file on filesystem. It must be a PEM encoded file which contains your certificate and private key. It can optionally contain the certificate chain of issuers. The private key also may be contained in a separate file specified by sslLocalPk. 
 - sslLocalPk: Path to local private key file on filesystem in case of separate files for certificate (sslLocalCert) and private key. 
 - sslPassphrase: Passphrase with which your sslLocalCert file was encoded. 

For example:

```php
use yii\httpclient\Client;

$client = new Client();

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
        'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
    ])
    ->send();
```

> Tip: you may setup default request options via [[\yii\httpclient\Client::$requestConfig]]. If you do so,
  use [[\yii\httpclient\Request::addOptions()]] to preserve their values, if you wish to add extra specific
  options for request.

You may as well pass options, which are specific for particular request transport. Usually it comes to this
in case of using [[\yii\httpclient\CurlTransport]]. For example: you may want to specify separated timeout
for connection and receiving data, which is supported by PHP cURL library. You can do this in following way:

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
]);

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
        CURLOPT_TIMEOUT => 10, // data receiving timeout
    ])
    ->send();
```

Please refer to the particular transport class docs for details about specific options support.