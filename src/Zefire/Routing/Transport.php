<?php

namespace Zefire\Routing;

use \Zefire\Core\ClosureSerializer;

class Transport
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
    protected $request;
    /**
     * Stores a route instance.
     *
     * @var \Zefire\Routing\Route
     */
    protected $route;
    /**
     * Stores a command instance.
     *
     * @var \Zefire\Routing\Command
     */
    protected $command;
    /**
     * Stores a list of compulsory middlewares
     * to be used.
     *
     * @var array
     */
	protected $middlewares = [];
    /**
     * Stores the middleware's callback.
     *
     * @var string
     */
    protected $method = 'handle';
    /**
     * Stores a list of actions.
     *
     * @var array
     */
    protected $action = [];
    /**
     * Creates a new transport instance.
     *
     * @return \Zefire\Routing\Transport
     */
    public function __construct()
    {
        $this->middlewares = (!empty(\App::config('services.middlewares'))) ? \App::config('services.middlewares') : [];
        return $this;
    }
    /**
     * Acquires the request for transport layer.
     *
     * @return \Zefire\Routing\Transport
     */
    public function send($request)
    {
    	$this->request = $request;
        return $this;
    }
    /**
     * Transports the request through each bounded middleware.
     *
     * @return \Zefire\Routing\Transport
     */
    public function through($route)
    {
        $this->route = $route;
        $this->gatherMiddlewares();
        foreach ($this->middlewares as $middleware) {
            $middleware = \Factory::make($middleware);
            $params = \Factory::resolveMethodDependencies($middleware, $this->method);
            call_user_func_array(array($middleware, $this->method), $params);
        }
        return $this;
    }
    /**
     * Excutes the route's action using the right controller and returns a response.
     *
     * @return mixed
     */
    public function execute()
    {
        if ($this->route['serialized_action']) {
            $this->route['action'] = unserialize($this->route['action']);            
        }
        if (is_object($this->route['action'])) {
            return $this->runClosure($this->route['action']);
        } else {
            $this->controller();
            $this->method();
            $controller = \Factory::make($this->action['controller']);
            $params = \Factory::resolveMethodDependencies($this->action['controller'], $this->action['method']);
            if ($this->route['method'] == 'get') {
                $params = array_merge($params, $this->getPlaceholderValue($this->parameters($this->route['uri']), $this->route['segments']));
            }
            return call_user_func_array(array($controller, $this->action['method']), $params);
        }        
    }
    /**
     * Runs a command.
     *
     * @return mixed
     */
    public function run($command)
    {
        $explode = explode('@', $command['action']);
        $controller = \Factory::make($explode[0]);
        $params = $this->request->input();
        if (method_exists($controller, $explode[1])) {
            return call_user_func_array(array($controller, $explode[1]), $params);
        } else {
            return 'Command not found';
        }        
    }
    /**
     * Gathers all required middleware for a given route.
     *
     * @return void
     */
    protected function gatherMiddlewares()
    {
        if (isset($this->route['middlewares'])) {
            array_walk($this->route['middlewares'], function($middleware) {
                if (!in_array($middleware, $this->middlewares)) {
                    array_push($this->middlewares, $middleware);    
                }            
            });    
        }        
    }
    /**
     * Exceutes a closure if the action was a closure
     * instead of a controller.
     *
     * @param  \Zefire\Core\ClosureSerializer $closure
     * @return mixed
     */
    protected function runClosure(ClosureSerializer $closure)
    {
        $this->action = $closure;
        return $closure();
    }
    /**
     * Extracts the controller from an action.
     *
     * @return void
     */
    protected function controller()
    {
        $explode = explode('@', $this->route['action']);
        $this->action['controller'] = 'App\\Controllers\\Http\\' . $explode[0];
    }
    /**
     * Extracts the method from an action.
     *
     * @return void
     */
    protected function method()
    {
        $explode = explode('@', $this->route['action']);
        $this->action['method'] = $explode[1]; 
    }
    /**
     * Extracts all uri parameters for a get request.
     *
     * @param  string $uri
     * @return array
     */
    protected function parameters($uri)
    {
        preg_match_all('/\{(.*?)\}/', $uri, $matches);
        return array_map(function ($m) {
            return trim($m, '?');
        }, $matches[0]);
    }
    /**
     * Gets a the correct value for a parameter on a get request.
     *
     * @param  array $params
     * @param  array $route_segments
     * @return string
     */
    protected function getPlaceholderValue($params, $route_segments)
    {
        $request_segments = $this->request->segments();
        $keys = [];
        foreach ($params as $param) {
            if (in_array($param, $route_segments)) {
                $keys[] = array_search($param, $route_segments);    
            }           
        }
        $values = [];
        foreach ($keys as $key) {
            $explode = explode('?', $request_segments[$key]);
            $values[] = $explode[0];
        }
        return $values;
    }
}