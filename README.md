# Browser
A library similar to perls WWW::Mechanize which manages a browser state and cookies

Requirements:
PHP ~5.3
That's it!

*Cookies are currently not functioning but will be implemented soon*

##Usage
```php
use Dash\Browser\Browser;

$browser = new Browser();

$browser->request('http://google.com','GET');

$response_status = $browser->getStatusCode();
$content_body    = $browser->getBody();
$header_array    = $browser->getResponseHeader();
```

