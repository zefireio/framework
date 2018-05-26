<?php

namespace Zefire\Controllers\Auth;

use Zefire\Http\Request;

class Authenticate
{
	/**
     * Default redirect URI.
     *
     * @var string
     */
	protected $redirect = '/';
	/**
     * Gets the login form.
     *
     * @return string
     */
	public function getLoginForm()
	{
		return \View::render('auth.login');
	}
	/**
     * Logs the user in the application.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function login(Request $request)
	{
		$inputs = $request->except('X-CSRF-TOKEN');
		$rules = [
            'email' 	=> 'required|email|max:255',
            'password' 	=> 'required'
        ];
        \Validator::validate($rules, $inputs);
        if (\Validator::passes()) {
            if (\Auth::login($inputs['email'], \Hasher::make($inputs['password']))) {
            	\Redirect::intended();
            } else {
            	\Redirect::to('/login');
            }
        }
	}
	/**
     * Logs the user out of the application.
     *
     * @return void
     */
	public function logout()
	{
		\Auth::logout();
		\Redirect::to('/');
	}
}
