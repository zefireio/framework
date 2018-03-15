<?php

namespace Zefire\Middlewares;

use Zefire\Contracts\Middleware;
use Zefire\Http\Request;

class PrepareResponseHeaders implements Middleware
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
    protected $request;
    /**
     * Stores a list of Mime Types.
     *
     * @var array
     */
	protected $mimeTypes = [
        'html' 	=> ['text/html', 'application/xhtml+xml'],
        'txt' 	=> ['text/plain'],
        'js' 	=> ['application/javascript', 'application/x-javascript', 'text/javascript'],
        'css' 	=> ['text/css'],
        'json' 	=> ['application/json', 'application/x-json'],
        'xml' 	=> ['text/xml', 'application/xml', 'application/x-xml'],
        'rdf' 	=> ['application/rdf+xml'],
        'atom' 	=> ['application/atom+xml'],
        'rss' 	=> ['application/rss+xml'],
        'form' 	=> ['application/x-www-form-urlencoded']
    ];
    /**
     * Creates a new prepare response heaaders instance.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
    /**
     * Handles response headers and stores them in header bag.
     *
     * @return void
     */
	public function handle()
    {
        $response_headers = $this->prepareResponseHeaders();
        if (!empty($response_headers)) {
        	foreach ($response_headers as $key => $value) {
        		\Header::set($key, $value);
        	}	
        }        
    }
    /**
     * Prepares response headers.
     *
     * @return array
     */
    protected function prepareResponseHeaders()
    {
    	$response_headers = [];
    	$request_headers = $this->request->getHeaders();
    	if (isset($request_headers['Content-Type'])) {
    		$response_headers['Content-Type'] = $this->getMimeType($request_headers['Content-Type']);
    	}
    	if (isset($request_headers['Accept'])) {
    		$response_headers['Content-Type'] = $this->getMimeType($request_headers['Accept']);
    	}
    	return $response_headers;
    }
    /**
     * Gets the Mime Type for a given header.
     *
     * @param  string $header
     * @return string
     */
    protected function getMimeType($header)
    {
    	foreach($this->mimeTypes as $key => $value) {
    		if (strstr($header, $key) >= 0) {
    			return $value[0];
    		}
    	}
    }
}