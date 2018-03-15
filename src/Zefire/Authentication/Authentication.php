<?php

namespace Zefire\Authentication;

use Zefire\Contracts\Connectable;
use Zefire\Factory\Factory;

class Authentication implements Connectable
{
    /**
     * Holds a Factory instance.
     *
     * @var \Zefire\Factory\Factory
     */
    protected $factory;
    /**
     * Holds the models instance providing users.
     *
     * @var mixed
     */
    protected $provider;
    /**
     * Create a new Authentication instance.
     *
     * @param. \Zefire\Factory\Factory
     * @return void
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;        
    }
    /**
     * Saves factory property on serialization.
     *
     * @return void
     */
    public function __sleep()
    {
        return array('factory');
    }
    /**
     * Gets a new instance of the provider
     * when instance is unserialized.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->getProvider();
    }
    /**
     * Gets a new instance of the provider.
     *
     * @return void
     */
    public function getProvider()
    {
        $provider = \App::config('auth.provider');
        $this->provider = $this->factory->make($provider);        
    }
    /**
     * Logs a user in the application using sessions.
     *
     * @param. string $email
     * @param. string $password
     * @return bool
     */
    public function login($email, $password)
    {
        $user = $this->provider->where('email', '=', $email)->where('password', '=', $password)->first();
        if ($user->id != null) {
            \Session::set('user', $user);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Determines if a user is logged in the application.
     *
     * @return bool
     */
    public function status()
    {
        return (\Session::exists('user')) ? true : false;
    }
    /**
     * Gets a logged user's information from session.
     *
     * @return mixed
     */
    public function user()
    {
        return \Session::get('user');
    }
    /**
     * Log's out a user from the application.
     *
     * @return bool
     */
    public function logout()
    {
        return \Session::forget('user');
    }
    /**
     * Api authentication based on token
     *
     * @return bool
     */
    public function token($token)
    {
        $user = $this->provider->where('api_token', '=', $token)->first();
        if ($user != null) {
            return true;
        } else {
            return false;
        }
    }
}