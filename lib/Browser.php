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

    private $request;

    private $response;

    public function __construct(CookieJar $cookie_jar = null, array $opt_args = array())
    {
        $this->cookie_jar = is_null($cookie_jar) ? new CookieJar()    : $cookie_jar;
        $this->debug = isset($opt_args['debug']) ? $opt_args['debug'] : false;
        $this->default_header = isset($opt_args['default_header']) ? $opt_args['default_header'] : array(
                'Accept' => 'text/html, application/xhtml+xml, */*',
                'Accept-Language' => 'en-US',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                'Proxy-Connection' => 'Keep-Alive',
            );
    }

    public function request($url, $method = 'GET', $header = array(), $content = '')
    {
        $this->request = new Request();
        $this->response = new Response();

        //request variables validation
        $this->request->setUrl($url);
        $this->request->setMethod($method);
        $this->request->setHeader($header);
        $this->request->setContent($content);

        $context = $this->parse_context($this->request->getMethod(), $this->request->getHeader(), $this->request->getContent());

        //build context and send request
        $context = stream_context_create($context);

        //send request
        $this->response->setContent(file_get_contents($this->req_url, false, $context));

        //parse header
        $this->response->setHeader($http_response_header);
        $this->response->setStatusCode($this->parse_status_code($this->res_header));

        return $this->response;
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

    private function parse_status_code($res_header)
    {
        $res_status = substr($res_header[0], 9, 3);

        $this->response->setStatusCode($res_status);

        return $res_status;
    }
}
