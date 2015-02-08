<?php
require_once("./CookieJar.php");
require_once("./Browser.php");
$cookies = new CookieJar();
$browser = new Browser($cookies);
/*$cookies->add("testkey=testval",".dashwinterson.com");
print_r($cookies);*/