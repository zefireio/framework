<?php

namespace Zefire\Middlewares;

use Zefire\Contracts\Middleware;
use Zefire\Http\Request;

class Cors implements Middleware
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
	protected $request;
	/**
     * Creates a new cors instance.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	/**
     * Handles CORS headers.
     *
     * @return void
     */
	public function handle()
	{
		if ($this->request->method() == 'OPTIONS') {
			if ($this->request->server('HTTP_ORIGIN') != null) {
				\Header::set('Access-Control-Allow-Origin', $this->request->server('HTTP_ORIGIN'));	
			} else {
				\Header::set('Access-Control-Allow-Origin', '*');	
			}			
			\Header::set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
			\Header::set('Access-Control-Allow-Headers', 'accept, content-type, authorization');
       } else {
           if ($this->request->server('HTTP_ORIGIN') != null) {
				\Header::set('Access-Control-Allow-Origin', $this->request->server('HTTP_ORIGIN'));	
			} else {
				\Header::set('Access-Control-Allow-Origin', '*');	
			}
       }
	}
}