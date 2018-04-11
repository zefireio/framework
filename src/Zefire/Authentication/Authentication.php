<?php

namespace Zefire\Authentication;

use Zefire\Factory\Factory;
use Zefire\Event\Dispatcher;

class Authentication
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
     * Stores a dispatcher instance.
     *
     * @var \Zefire\Event\Dispatcher
     */
    protected $dispatcher;
    /**
     * Create a new Authentication instance.
     *
     * @param  \Zefire\Factory\Factory
     * @param  \Zefire\Event\Dispatcher
     * @return void
     */
    public function __construct(Factory $factory, Dispatcher $dispatcher)
    {
        $this->factory = $factory;        
        $this->dispatcher = $dispatcher;
    }
    /**
     * Saves factory property on serialization.
     *
     * @return void
     */
    public function __sleep()
    {
        return array('factory', 'dispatcher');
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
        if ($user->hasResults()) {
            \Session::set('user', $user);
            $this->dispatcher->queue('app-auth', ['status' => true, 'user' => $user->email]);
            return true;
        } else {
            $this->dispatcher->queue('app-auth', ['status' => false, 'user' => $email]);
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
        $user = \Session::get('user');
        $logout = \Session::forget('user');
        $this->dispatcher->queue('app-logout', ['user' => $user->email]);
        return $logout;
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