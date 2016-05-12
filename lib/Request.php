<?php
/**
 * Created by PhpStorm.
 * User: dashdanw
 * Date: 5/12/16
 * Time: 7:33 AM.
 */

namespace Dash\Browser;

class Request
{
    private $url;
    private $method;
    private $header;
    private $content;

    private $http_methods = array('CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT', 'TRACE', 'PATCH');

    /**
     * @param string $url
     * @param string $method
     * @param string $header
     * @param string $content
     */
    public function __construct($url = '', $method = '', $header = '', $content = '')
    {
        $this->url = $url;
        $this->method = $method;
        $this->header = $header;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
        } else {
            throw new BrowserException('Supplied url was not valid!');
        }
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if (in_array($method, $this->http_methods)) {
            $this->method = $method;
        } else {
            throw new BrowserException('Undefined HTTP method');
        }
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $header_merged = array_merge($this->default_header, $req_header);
        $header = '';
        foreach ($header_merged as $name => $value) {
            $header .= "$name: $value\r\n";
        }

        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
    }
}
