<?php
/**
 * Created by PhpStorm.
 * User: dashdanw
 * Date: 5/12/16
 * Time: 7:33 AM.
 */

namespace Dash\Browser;

class Response
{
    private $status_code;
    private $header;
    private $content;

    public function __construct($status_code = '', $header = '', $content = '')
    {
        $this->status_code = $status_code;
        $this->header = $header;
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param mixed $status_code
     */
    public function setStatusCode($status_code)
    {
        if (!is_int($status_code) || $status_code < 100 || $status_code >= 600) {
            throw new BrowserException('Response code was not parsed correctly, header value is '.$res_header[0]);
        } elseif ($status_code >= 400) {
            throw new BrowserException('Server response code indicated an error', $status_code);
        }
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
