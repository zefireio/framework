<?php

namespace Zefire\Redirect;

use Zefire\Http\Request;

class Redirect
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
	protected $request;
	/**
     * Creates a new redirect instance.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	/**
     * Redirects to a given URI.
     *
     * @param  string $to
     * @param  array  $inputs
     * @return void
     */
	public function to($to, $inputs = [])
	{
		$this->redirect($to, $inputs);
	}
	/**
     * Redirects to previous URI.
     *
     * @param  array $inputs
     * @return void
     */
	public function back($inputs = [])
	{
		$referer = ($this->request->referer()) ? $this->request->referer() : '/';
		$this->redirect($referer, $inputs);		
	}
	/**
     * Sets intended URI to session for later use.
     *
     * @param  string $uri
     * @return void
     */
	public function setIntended($uri)
	{
		\Session::set('intended', $uri);
	}
	/**
     * Redirects to intended URI.
     *
     * @return void
     */
	public function intended()
	{
		$intended = \Session::get('intended');
		if ($intended != null) {
			\Session::forget('intended');
			$this->redirect($intended);	
		} else {
			$this->redirect('/');
		}		
	}
	/**
     * Performs actual redirect to a given location.
     *
     * @param  string $url
     * @param  array  $inputs
     * @return void
     */
	protected function redirect($url, $inputs = [])
	{
		if (!empty($inputs)) {
			$queryString = '?';
			foreach ($inputs as $key => $value) {
				$queryString .= $key . '=' . $value . '&';
			}
			$queryString = substr($queryString, 0, -1);
			header('Location:' . $url . $queryString);
		} else {
			header('Location:' . $url);
		}
	}
}