<?php

namespace Zefire\Routing;

use Zefire\Core\ClosureSerializer;

class Route
{
	/**
     * Stores a list of registered routes.
     *
     * @var array
     */
	protected $routes = [];
	/**
     * Stores current route.
     *
     * @var \Zefire\Routing\Route
     */
	protected $current;
	/**
     * Registers a GET route.
     *
     * @param  string $uri
     * @param  string $action
     * @return \Zefire\Routing\Route
     */
	public function get($uri, $action)
	{
		$this->add('get', $uri, $action);
		return $this;
	}
	/**
     * Registers a POST route.
     *
     * @param  string $uri
     * @param  string $action
     * @return \Zefire\Routing\Route
     */
	public function post($uri, $action)
	{
		$this->add('post', $uri, $action);
		return $this;
	}
	/**
     * Registers a PUT route.
     *
     * @param  string $uri
     * @param  string $action
     * @return \Zefire\Routing\Route
     */
	public function put($uri, $action)
	{
		$this->add('put', $uri, $action);
		return $this;
	}
	/**
     * Registers a PATCH route.
     *
     * @param  string $uri
     * @param  string $action
     * @return \Zefire\Routing\Route
     */
	public function patch($uri, $action)
	{
		$this->add('patch', $uri, $action);
		return $this;
	}
	/**
     * Registers a DELETE route.
     *
     * @param  string $uri
     * @param  string $action
     * @return \Zefire\Routing\Route
     */
	public function delete($uri, $action)
	{
		$this->add('delete', $uri, $action);
		return $this;
	}
	/**
     * Defines a middleware to be used with a route.
     *
     * @param  mixed $middlewares
     * @return \Zefire\Routing\Route
     */
	public function middleware($middlewares)
	{
		if (!is_array($middlewares)) {
			$explode = explode('|', $middlewares);
			$middlewares = $explode;
		}
		foreach ($middlewares as $key => $value) {
			$this->current['middlewares'][$key] = $value;
			$this->routes[$this->current['method']][$this->current['id']]['middlewares'][$key] = 'App\\Middlewares\\' . $value;
		}
		\App::bind(get_class($this), $this);
		return $this;			
	}
	/**
     * Gets all registered routes.
     *
     * @return array
     */
	public function getRoutes()
	{
		return $this->routes;
	}
	/**
     * Performs the actual route registration.
     *
     * @param  string   $method
     * @param  string   $uri
     * @param  \Closure $action
     * @return void
     */
	protected function add($method, $uri, $action)
	{
		$serialized_action = false;
		if ($action instanceOf \Closure) {
			$closure = new ClosureSerializer($action);
			$action = serialize($closure);
			$serialized_action = true;
		}
		$route = [
			'id' 				=> \Hasher::make($method . $uri, 'sha1'),
			'method' 			=> $method,
			'uri' 				=> $uri,
			'action' 			=> $action,
			'serialized_action' => $serialized_action
		];
		if ($method == 'get') {
			$route['parameters'] = $this->parameters($uri);
		}
		$route['segments'] 		= $this->segments($uri);
		$route['segment_count'] = count($route['segments']);
		$this->current 			= $route;
		$this->routes[$method][$this->current['id']] = $route;		
	}
	/**
     * Extracts route segments.
     *
     * @param  string $uri
     * @return array
     */
	protected function segments($uri)
    {
        $segments = explode('/', $uri);
        return array_values(array_filter($segments, function ($v) {
            return $v !== '';
        }));
    }
    /**
     * Extracts parameters from GET routes.
     *
     * @param  string $uri
     * @return array
     */
    protected function parameters($uri)
	{
		preg_match_all('/\{(.*?)\}/', $uri, $matches, PREG_PATTERN_ORDER);
		$parameters = [];
		foreach ($matches[0] as $key => $value) {
			$parameters[] = $value;				
		}	
		return $parameters;		
	}
}