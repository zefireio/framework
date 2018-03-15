<?php

namespace Zefire\Controllers\Auth;

use Zefire\Http\Request;
use App\Models\User;

class Registration
{
	/**
     * Default redirect URI.
     *
     * @var string
     */
    protected $redirect = '/';
    /**
     * Gets the registration form.
     *
     * @return string
     */
	public function getRegistrationForm()
	{
		return \View::render('auth.register');
	}
    /**
     * Registers a new user in the application.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function register(Request $request)
	{
		$inputs = $request->input();
        $rules = [
            'email' 	=> 'required|unique:user|email|max:255',
            'password' 	=> 'min:6'
        ];
        \Validator::validate($rules, $inputs);
        if (\Validator::passes()) {
        	$inputs['password'] = \Hasher::make($inputs['password']);
        	$model = new User();
            $user = $model->create($inputs);
            if (\Auth::login($user->email, $user->password)) {
            	\Redirect::to($this->redirect);
            } else {
            	\Redirect::to('/auth/register');
            }
        }
	}
}