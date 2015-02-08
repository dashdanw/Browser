<?php
/**
 * class for parsing LWP and other cookie for use with a Browser object
 * LWP PARSING NOT YET IMPLEMENTED
 */
class CookieJar
{
    public $cookies;
    /**
     * builds a cookie object with
     * @param mixed $cookies can be an LWP file or LWP string, otherwise will generate a blank cookie object
     */
    public function __construct($cookies = "")
    {
        if (is_file($cookies)) {
            $this->cookies = $this->parse_lwp_cookie_file($cookies);
        } elseif (is_string($cookies) && strlen($cookies)) {
            $this->cookies = $this->parse_lwp_cookie_string($cookies);
        } else {
            $this->cookies = array();
        }

        return $this;
    }

    public function addCookie($key_value, $domain, $path = "/", $expire = false, $secure = false, $http_only = false)
    {
        list($key, $value) = $this->parse_cookie_val($key_value);

        $this->cookies[$domain][$key]["value"]     = $value;
        $this->cookies[$domain][$key]["path"]      = $this->parse_cookie_path($path);
        $this->cookies[$domain][$key]["expire"]    = $this->parse_cookie_expire($expire);
        $this->cookies[$domain][$key]["http_only"] = $this->parse_cookie_http_only($http_only);
        $this->cookies[$domain][$key]["secure"]    = $this->parse_cookie_secure($secure);

        return $this;
    }

    public function delCookie($key, $domain, $path = "/")
    {
        if (!count($this->cookies[$domain])) {
            unset($this->cookies[$domain]);
        } else {
            unset($this->cookies[$domain][$key]);
        }

        return $this;
    }

    public function getCookies($domain, $path, $secure = false, $http_only = false)
    {
        $cookie_str = "";

        foreach ($this->cookies[$domain] as $key => $cookie) {
            //if secure or http_only is set true then the cookie needs to meet those requirements
            if ((!$secure || $cookie["secure"]) && (!$http_only || $cookie["http_only"])) {
                $cookie_str .= $key."=".$cookie["value"]."; ";
            }
        }

        return $cookie_str;
    }

    /*
    COOKIE PARAMETER PARSING
     */
    private function parse_cookie_val($value)
    {
        $val_regex = "/^[0-9a-zA-Z!#$%&'()*+.\/<=>?@[\]{|}~^_`:]+\=[0-9a-zA-Z!#$%&'()*+.\/<=>?@[\]{|}~^_`:]+$/";
        if (preg_match($val_regex, $value)) {
            $key_value = explode("=", $value);
        } else {
            throw new Exception("cookies value contained invalid characters or was of an invalid format");
        }

        return $key_value;
    }
    private function parse_cookie_path($path)
    {
        return $path;
    }
    private function parse_cookie_expire($expire)
    {
        if (!is_bool($expire) || ($expire = strtotime($expire))) {
            throw new Exception("cookie attribute \"expire\" must be a boolean false or a valid datetime string");
        }

        return $expire;
    }
    private function parse_cookie_http_only($http_only)
    {
        if (!is_bool($http_only)) {
            throw new Exception("cookie attribute \"httpOnly\" must be a boolean");
        }

        return $http_only;
    }

    /*
    LWP PARSING
     */

    private function parse_lwp_cookie_file($cookie_file)
    {
        $cookie_string = file_get_contents($cookie_file);

        return $this->parse_lwp_cookie_string($cookie_string);
    }
    private function parse_lwp_cookie_string($cookie_string)
    {
        $cookie_string = $this->validate_lwp_cookie_string($cookie_string);
        $cookie_string = preg_replace("/^\#LWP\-Cookies\-2\.0\s*\n/", "", $cookie_string);
        $cookie_elems  = preg_split("/\n?Set\-Cookie(2|3)\:\ /");
        foreach ($cookie_elems as $cookie_elem) {
            //WIP
        }
    }
    private function validate_lwp_cookie_string($cookie_string)
    {
        $validate_regex = "/^\#LWP\-Cookies\-2\.0\s*(\nSet\-Cookie(2|3)\:(\s(.)+\=(.)+;)+)+\n$/";
        if (!preg_match($validate_regex, $cookie_string)) {
            throw new Exception("String was not a valid LWP 2.0 cookie format");
        } else {
            return $cooke_string;
        }
    }
}
