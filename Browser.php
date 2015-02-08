<?php
/**
 * Stateful browser (keeps track of current status)
 */
class Browser
{
    private $cookie_jar;

    private $user_agent;

    private $req_url;
    private $req_method;
    private $req_params;
    private $req_header;

    private $res_status;
    private $res_headers;
    private $res_content;

    public function __construct(CookieJar $cookie_jar, $user_agent = false, $debug = false)
    {
        $this->cookie_jar = $cookie_jar;
        $this->user_agent = ($user_agent) ? $user_agent : "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0";
    }
    public function request($url, $method = false, $params = false)
    {
        //rudimentary setting of request variables for browser state
        $this->req_url    = $url;
        $this->req_method = ($method) ? $method : "GET";
        $this->req_params = ($params) ? $params : parse_url($url, PHP_URL_QUERY);

        //run through parsing the header
        $this->req_header = $this->parse_header();

        //request variables validation
        $this->req_url    = $this->parse_url();
        $this->req_method = $this->parse_method();
        $this->req_params = $this->parse_params();

        //build context and send request
        $context_stream    = stream_context_create($this->build_context());
        $this->res_content = file_get_contents($this->url, false, $context_stream);

        //parse headers
        $this->res_headers = $http_response_headers;
        $this->res_status  = $this->parse_res_status($this->res_headers);

        /*$this->req_context = array(
    		$url_components["scheme"] => array(
    			"method" => ,
    			"header" => "Cookie: " . $this->cookie_jar->getCookies($this->req_url) . "\r\n" .
            				"User-Agent: " . $this->user_agent . "\r\n",
            	"content"=> ($params) ? $params : $url_components["query"]
    		);
    	);*/
    }
    private function parse_url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        } else {
            throw new Exception("Supplied url was not valid!");
        }
    }
    private function parse_method($method)
    {
    	$method = strtoupper($method);
        if( $method == "POST" || $method == "GET" ){
        	return $method;
        } else {
        	throw new Exception("Undefined HTTP method");
        }
    }
    private function parse_params($params)
    {
        return $params;
    }
    private function parse_res_status($res_headers)
    {
        $res_status = substr($res_headers[0], 9, 3);
        $res_status = filter_var($res_status, FILTER_VALIDATE_INT);

        if (!$res_status || $res_status < 100 || $res_status >= 600) {
            throw new Exception("Response code was not parsed correctly, header value is ".$res_headers[0]);
        } elseif ($res_status >= 400 && $this->debug) {
            throw new Exception("Server response code indicated an error",$res_status);
        }

        return $res_status;
    }
}
