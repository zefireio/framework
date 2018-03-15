<?php

namespace Zefire\Http;

class Response
{
    /**
     * Stores the response's headers.
     *
     * @var array
     */
    protected $headers;
    /**
     * Stores the response's content.
     *
     * @var array
     */
    protected $content;
    /**
     * Stores the response's Http status code.
     *
     * @var array
     */
    protected $code;
    /**
     * Stores the response's Http version.
     *
     * @var array
     */
    protected $version = '1.0';
    /**
     * Creates a new response instance.
     *
     * @param int    $code
     * @param string $content
     * @return void
     */
    public function __construct($code = 200, $content = '')
    {
        $this->code = $code;
        $this->text = \App::config('http.' . $this->code);
        $this->content = $content;
        $this->headers = \Header::all();        
    }
    /**
     * Sets the response's headers.
     *
     * @param array $headers
     * @return void
     */
    public function headers(array $headers = [])
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
    }
    /**
     * Sets the response's content.
     *
     * @param string $content
     * @return void
     */
    public function content($content)
    {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
            if (is_array($content) || is_object($content)) {
                $content = json_encode($content);
            } else {
                throw new \Exception(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));    
            }            
        }
        $this->content = (string) $content;
        return $this;
    }
    /**
     * Sends the response to browser.
     *
     * @return \Zefire\Http\Response
     */
    public function send()
    {
        if (headers_sent()) {
            return $this;
        } else {
            foreach ($this->headers as $key => $value) {
                header($key . ': ' . $value, false, $this->code);                
            }
        }
        header(sprintf('HTTP/%s %s %s', $this->version, $this->code, $this->text), true, $this->code);
        if (is_array($this->content) || is_object($this->content)) {
            echo json_encode($this->content);
        } else {
            echo $this->content;    
        }
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        return $this;
    }
    /**
     * Gets the response's Http status code.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * Sets the response's charset.
     *
     * @param string $charset
     * @return \Zefire\Http\Response
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }
    /**
     * Gets the response's charset.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }
    /**
     * Checks if the response is valid.
     *
     * @return int
     */
    public function isInvalid()
    {
        return ($this->code < 100 || $this->code >= 600) ? 1 : 0;
    }
    /**
     * Checks if the response is informational.
     *
     * @return int
     */
    public function isInformational()
    {
        return ($this->code >= 100 && $this->code < 200) ? 1 : 0;
    }
    /**
     * Checks if the response is successful.
     *
     * @return int
     */
    public function isSuccessful()
    {
        return ($this->code >= 200 && $this->code < 300) ? 1 : 0;
    }
    /**
     * Checks if the response is a redirect.
     *
     * @return int
     */
    public function isRedirection()
    {
        return ($this->code >= 300 && $this->code < 400) ? 1 : 0;
    }
    /**
     * Checks if the response is client error.
     *
     * @return int
     */
    public function isClientError()
    {
        return ($this->code >= 400 && $this->code < 500) ? 1 : 0;
    }
    /**
     * Checks if the response is server error.
     *
     * @return int
     */
    public function isServerError()
    {
        return ($this->code >= 500 && $this->code < 600) ? 1 : 0;
    }
    /**
     * Checks if the response is ok.
     *
     * @return int
     */
    public function isOk()
    {
        return ($this->code == 200) ? 1 : 0;
    }
    /**
     * Checks if the response is forbidden.
     *
     * @return int
     */
    public function isForbidden()
    {
        return ($this->code == 403) ? 1 : 0;
    }
    /**
     * Checks if the response is not found.
     *
     * @return int
     */
    public function isNotFound()
    {
        return ($this->code == 404) ? 1 : 0;
    }
    /**
     * Checks if the response is empty.
     *
     * @return int
     */
    public function isEmpty()
    {
        return in_array($this->code, array(204, 304));
    }
    /**
     * Return the response as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('HTTP/%s %s %s', $this->version, $this->code, $this->text) . "\r\n" . $this->headers . "\r\n" . $this->getContent();
    }
}