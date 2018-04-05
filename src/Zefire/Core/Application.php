<?php

namespace Zefire\Core;

use Zefire\Core\Container;
use Zefire\Config\Config;
use Dotenv\Dotenv;
use Zefire\Router\Router;
use Zefire\Router\Pipeline;
use Zefire\Helpers\Arr;

class Application
{
    /**
     * Stores an application instance.
     *
     * @var \Zefire\Core\Application
     */
    protected static $appInstance;
    /**
     * Stores a container instance.
     *
     * @var \Zefire\Core\Container
     */
    protected $container;
    /**
     * Stores a list of loaded services.
     *
     * @var array
     */
    protected $services = [];
    /**
     * Stores a list of loaded aliases.
     *
     * @var array
     */
    protected $aliases = [];
    /**
     * Stores the application's base path.
     *
     * @var string
     */
    protected $basePath;
    /**
     * Stores the application's running mode.
     *
     * @var string
     */
    protected $runningMode;
    /**
     * Creates a new application instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Load Container
        $this->container = new Container();
        // Set Base Path
        $this->setBasePath();
        // Set Running Mode
        $this->setRunningMode();
        // Bind Application service to container and create the App Alias
        $this->bind('Zefire\Core\Application', $this);
        $this->alias('App', 'Zefire\Aliases\App');
        $this->aliases['App'] = 'Zefire\Aliases\App';
        // Store Application instance as static property
        self::$appInstance = $this;
    }
    /**
     * Boots the application.
     *
     * @return \Zefire\Core\Application
     */
    public function boot()
    {
        // Load ENV file if exists
        $this->loadEnvironmentFile();
        // Get list of services to load
        $services = $this->config('services.services');
        // Load services
        array_walk($services, function($class) {
            $this->make($class);
            $this->services = $class;
        });
        // Load Aliases
        foreach ($this->config('services.aliases') as $key => $value) {
            $this->alias($key, $value);
            $this->aliases[$key] = $value;            
        }
        // Load Helpers
        include $this->zefirePath() . DIRECTORY_SEPARATOR. 'Helpers' . DIRECTORY_SEPARATOR . 'helpers.php';
        // Set error handlers
        $this->setErrorHandlers();
        // Set CSRF Token
        $this->csrfToken();
        // Set kernel load time to session.
        \Session::set('kernel_runtime', \App::runtime());
        // Dispatching kernel loaded event
        \Dispatcher::queue('app-message', ['message' => 'Request runtime ' . \Session::get('kernel_runtime')]);
        return $this;
    }
    /**
     * Returns an application instance.
     *
     * @return \Zefire\Core\Application
     */
    public static function getApp()
    {
        return self::$appInstance;
    }
    /**
     * Returns the application's running mode.
     *
     * @return string
     */
    public function runningMode()
    {
        return $this->runningMode;
    }
    /**
     * Handles a request.
     *
     * @return mixed
     */
    public function handle()
    {
        return \Router::handle();
    }
    /**
     * Creates a new instance of a service
     * or pulls it from the container if it already exists.
     *
     * @return mixed
     */
    public function make($class, $params = [])
    {    
        return $this->container->make($class, $params);
    }
    /**
     * Binds an instance of a service to container.
     *
     * @return void
     */
    public function bind($key, $instance)
    {    
        $this->container->bind($key, $instance);
    }
    /**
     * Resolves dependencies or pulls them from the
     * container if they already exists.
     *
     * @param  string $class
     * @param  string $method
     * @return array
     */
    public function resolveMethodDependencies($class, $method)
    {    
        return $this->container->resolveMethodDependencies($class, $method);
    }
    /**
     * Registers a new alias for a given service.
     *
     * @param  string $name
     * @param  string $class
     * @return void
     */
    public function alias($name, $class)
    {
        $this->container->registerAlias($name, $class);
    }    
    /**
     * Deletes a bounded service from the container.
     *
     * @param  string $serviceName
     * @return void
     */
    public function forgetInstance($serviceName)
    {
        $this->container->forget($serviceName);
    }
    /**
     * Returns a list of services bounded to the container.
     *
     * @return array
     */
    public function listContainer()
    {
        return array_keys($this->container->bindings);
    }
    /**
     * Returns a list of aliases.
     *
     * @return array
     */
    public function listAliases()
    {
        return array_keys($this->aliases);
    }
    /**
     * Returns the application's debug setting.
     *
     * @return bool
     */
    public function debugMode()
    {
        return ($this->config('app.debug') == 'true') ? true : false;
    }
    /**
     * Returns the application's host setting.
     *
     * @return string
     */
    public function host()
    {
        return $this->config('app.host');
    }
    /**
     * Retrieves settings for a given key.
     *
     * @return mixed
     */
    public function config($key)
    {
        return Arr::get($key);
    }    
    /**
     * Returns the application's base path.
     *
     * @return string
     */
    public function basePath()
    {    
        return $this->basePath;
    }
    /**
     * Returns the app folder path.
     *
     * @return string
     */
    public function appPath()
    {    
        return $this->basePath . 'app';
    }    
    /**
     * Returns the vendor's folder path.
     *
     * @return string
     */
    public function vendorPath()
    {    
        return $this->basePath . 'vendor';
    }
    /**
     * Returns the framework's folder path.
     *
     * @return string
     */
    public function zefirePath()
    {    
        return $this->vendorPath() . DIRECTORY_SEPARATOR . 'zefireio' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR .  'src' . DIRECTORY_SEPARATOR . 'Zefire';
    }
    /**
     * Returns the config folder path.
     *
     * @return string
     */
    public function configPath()
    {    
        return $this->basePath . 'config';
    }
    /**
     * Returns the routing folder path.
     *
     * @return string
     */
    public function routingPath()
    {    
        return $this->basePath . 'routing';
    }
    /**
     * Returns the resource folder path.
     *
     * @return string
     */
    public function resourcesPath()
    {    
        return $this->basePath . 'resources';
    }
    /**
     * Returns the assets folder path.
     *
     * @return string
     */
    public function assetsPath()
    {    
        return $this->basePath . 'resources' . DIRECTORY_SEPARATOR . 'assets';
    }
    /**
     * Returns the translation folder path.
     *
     * @return string
     */
    public function translatePath()
    {
        return $this->basePath . 'resources' . DIRECTORY_SEPARATOR . 'lang';
    }
    /**
     * Returns the template folder path.
     *
     * @return string
     */
    public function templatePath()
    {    
        return $this->basePath . 'resources' . DIRECTORY_SEPARATOR . 'templates';
    }
    /**
     * Returns the compiled template folder path.
     *
     * @return string
     */
    public function compiledPath()
    {    
        if (!file_exists($this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'views')) {
            mkdir($this->basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'views', 0755, true);
        }
        return $this->basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'views';
    }
    /**
     * Returns the log folder path.
     *
     * @return string
     */
    public function logPath()
    {
        if (!file_exists($this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'logs')) {
            mkdir($this->basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs', 0755, true);
        }
        return $this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'logs';
    }
    /**
     * Returns the session folder path.
     *
     * @return string
     */
    public function sessionPath()
    {
        if (!file_exists($this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'sessions')) {
            mkdir($this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'sessions', 0755, true);
        }
        return $this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'sessions';
    }
    /**
     * Returns the storage folder path.
     *
     * @return string
     */
    public function storagePath()
    {
        if (!file_exists($this->basePath . 'storage')) {
            mkdir($this->basePath . 'storage', 0755, true);
        }
        return $this->basePath . 'storage';
    }
    /**
     * Returns runtime.
     *
     * @return float
     */
    public function runtime()
    {
        $runtime = round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 3);
        if ($runtime < 1) {
            $runtime = ($runtime * 1000) . 'ms';
        } else {
            $runtime = $runtime . 's';
        }
        return $runtime;
    }
    /**
     * Returns the application's maintenance mode setting.
     *
     * @return string
     */
    public function maintenanceMode()
    {
        return (file_get_contents(\App::storagePath() . DIRECTORY_SEPARATOR . 'Zefire') == 'true') ? true : false;
    }
    /**
     * Generates CSRF Token and stores it into session.
     *
     * @return string
     */
    public function csrfToken()
    {
        $token = \Session::get('XSRF-TOKEN');
        if ($token == null) {
            $token = \Hasher::make(uniqid(rand(), true));
            \Session::set('XSRF-TOKEN', $token);
            \Cookie::set('XSRF-TOKEN', $token);
        }        
        return $token;
    }
    /**
     * Sets the application's base path.
     *
     * @return void
     */
    protected function setBasePath()
    {
        $this->basePath = (PHP_SAPI === 'cli') ? $_SERVER['PWD'] . DIRECTORY_SEPARATOR : str_replace('/public', '', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR);
    }
    /**
     * Sets the application's running mode flag.
     *
     * @return void
     */
    protected function setRunningMode()
    {
        $this->runningMode = (PHP_SAPI === 'cli') ? 'cli' : 'http';
    }
    /**
     * Loads the environment's settings.
     *
     * @return void
     */
    protected function loadEnvironmentFile()
    {
        if (file_exists($this->basePath() . '.env')) {
            $dotenv = new Dotenv($this->basePath());
            $dotenv->load();    
        }
    }
    /**
     * Sets the application's error handlers.
     *
     * @return void
     */
    protected function setErrorHandlers()
    {
        error_reporting(-1);
        set_exception_handler($this->config('app.exception_handler'));
        set_error_handler($this->config('app.error_handler'));
        register_shutdown_function($this->config('app.shutdown_handler'));
        if ($this->config('app.debug') === false) {
            ini_set('display_errors', 'Off');
        }
    }
}