<?php
/**
 * Stateful browser (keeps track of current status).
 */

namespace Dash\Browser;

class Browser
{
    private $cookie_jar;

    private $default_header;

    private $debug;

    private $req_url;
    private $req_method;
    private $req_header;
    private $req_content;

    private $res_status;
    private $res_header;
    private $res_content;

    private $http_methods = array( 'CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT', 'TRACE', 'PATCH' );

    public function __construct(CookieJar $cookie_jar = null, array $opt_args = array())
    {
        $this->cookie_jar = is_null($cookie_jar)           ? new CookieJar()         : $cookie_jar;
        $this->debug = isset($opt_args['debug'])      ? $debug                  : false;
        $this->default_header = isset($opt_args['default_header']) ? $opt_args['default_header'] : array(
                'Accept' => 'text/html, application/xhtml+xml, */*',
                'Accept-Language' => 'en-US',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                'Proxy-Connection' => 'Keep-Alive',
            );
    }

    public function getStatusCode()
    {
        return $this->res_status;
    }
    public function getResponseHeader()
    {
        return $this->res_header;
    }
    public function getBody()
    {
        return $this->res_header;
    }

    public function request($url, $method = 'GET', $header = array(), $content = '')
    {
        //remove previous request data
        $this->flush_context();

        //request variables validation
        $this->req_url = $this->parse_url($url);
        $this->req_method = $this->parse_method($method);
        $this->req_header = $this->parse_header($header);
        $this->req_context = $this->parse_context($this->req_method, $this->req_header, (string) $content);

        //build context and send request
        $context = stream_context_create($this->req_context);

        //send request
        $this->res_content = file_get_contents($this->req_url, false, $context);

        //parse header
        $this->res_header = $http_response_header;
        $this->res_status = $this->parse_res_status($this->res_header);

        return $this->res_content;
    }
    private function parse_url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        } else {
            throw new BrowserException('Supplied url was not valid!');
        }
    }
    private function parse_method($method)
    {
        $method = strtoupper($method);
        if (in_array($method, $this->http_methods)) {
            return $method;
        } else {
            throw new BrowserException('Undefined HTTP method');
        }
    }
    private function parse_header($req_header)
    {
        $header_merged = array_merge($this->default_header, $req_header);
        $header = '';
        foreach ($header_merged as $name => $value) {
            $header .= "$name: $value\r\n";
        }

        return $header;
    }
    private function parse_context($method, $header = '', $content = '')
    {
        return array(
            'http' => array(
                'method' => $method,
                'header' => $header.$this->cookie_jar->getCookies(),
                'content' => $content,
            ),
        );
    }
    private function flush_context()
    {
        unset($req_url);
        unset($req_method);
        unset($req_header);
        unset($req_content);
        unset($res_status);
        unset($res_header);
        unset($res_content);
    }
    private function parse_res_status($res_header)
    {
        $res_status = substr($res_header[0], 9, 3);
        $res_status = filter_var($res_status, FILTER_VALIDATE_INT);

        if (!isset($res_status) || $res_status < 100 || $res_status >= 600) {
            throw new BrowserException('Response code was not parsed correctly, header value is '.$res_header[0]);
        } elseif ($res_status >= 400 && $this->debug) {
            throw new BrowserException('Server response code indicated an error', $res_status);
        }

        return $res_status;
    }
}
