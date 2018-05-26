<?php

namespace Zefire\Middlewares;

use Zefire\Contracts\Middleware;
use Zefire\Http\Request;

class Authorization implements Middleware
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
	protected $request;
	/**
     * Creates a new authorization instance.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	/**
     * Authorize a user to access a resource.
     *
     * @return void
     */
	public function handle()
	{
		if (!\Auth::status()) {
			\Redirect::setIntended($this->request->uri());
			\Redirect::to('/login');
		}		
	}
}
