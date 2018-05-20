<?php

namespace Zefire\Console;

class Controller
{
	/**
     * Clears all compiled views.
     *
     * @return string
     */
	public function clearViews()
	{
		$this->clearFiles('compiled');
		return 'Cleared compiled views';
	}
	/**
     * Clears all session files.
     *
     * @return string
     */
	public function clearSessions()
	{
		$this->clearFiles('sessions');
		return 'Cleared file sessions';
	}
	/**
     * Clears the log file.
     *
     * @return string
     */
	public function clearLogs()
	{
		$this->clearFiles('logs');
		return 'Cleared log files';
	}
	/**
     * Starts queue worker.
     *
     * @return void
     */
	public function work($queue = 'default')
	{
		\Queue::listen($queue);
	}
	/**
     * Clears all jobs on queue.
     *
     * @return string
     */
	public function clearQueue($queue = 'default')
	{
		return \Queue::clearQueue($queue);
	}
	/**
     * Gets the Application out of maintenance mode.
     *
     * @return string
     */
	public function up()
	{
		\FileSystem::disk('storage')->put('Zefire', '');
		return 'Zefire is up';		
	}
	/**
     * Puts the application in maintenance mode.
     *
     * @return string
     */
	public function down()
	{
		\FileSystem::disk('storage')->put('Zefire', 'true');
		return 'Zefire is in maintenance mode';
	}
	/**
     * Generates an application controller from command line.
     *
     * @param  string $namespace
     * @param  string $controller
     * @return string
     */
	public function generateController($namespace, $controller)
	{
		$string = "<?php
			
namespace " . $namespace . ";

class " . $controller . "
{
	public function index()
	{
    	//
	}
}";
		$directory = \App::appPath() . str_replace('\\', DIRECTORY_SEPARATOR, str_replace('App\\', '', $namespace));
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0755, true)) {
    			throw new \Exception('Failed to create directory: ' . $directory);
			}
		}
		\File::put($directory . DIRECTORY_SEPARATOR . $controller . '.php', $string);
		return $namespace . '\\' .  $controller . ' controller has been created.';
	}
	/**
     * Generates an application middleware from command line.
     *
     * @param  string $namespace
     * @param  string $middleware
     * @return string
     */
	public function generateMiddleware($namespace, $middleware)
	{
		$string = "<?php

namespace " . $namespace . ";

use Zefire\Contracts\Middleware;

class " . $middleware . " implements Middleware
{
	public function handle()
	{
		//		
	}
}";
		$directory = \App::appPath() . str_replace('\\', DIRECTORY_SEPARATOR, str_replace('App\\', '', $namespace));
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0755, true)) {
    			throw new \Exception('Failed to create directory: ' . $directory);
			}
		}
		\File::put($directory . DIRECTORY_SEPARATOR . $middleware . '.php', $string);
		return $namespace . '\\' .  $middleware . ' middleware has been created.';
	}
	/**
     * Generates an application job from command line.
     *
     * @param  string $namespace
     * @param  string $job
     * @return string
     */
	public function generateJob($namespace, $job)
	{
		$string = "<?php

namespace " . $namespace . ";

use Zefire\Contracts\Handleable;

class " . $job . " implements Handleable
{
	public function handle()
	{
		//
	}
}";
		$directory = \App::appPath() . str_replace('\\', DIRECTORY_SEPARATOR, str_replace('App\\', '', $namespace));
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0755, true)) {
    			throw new \Exception('Failed to create directory: ' . $directory);
			}
		}
		\File::put($directory . DIRECTORY_SEPARATOR . $job . '.php', $string);
		return $namespace . '\\' .  $job . ' job has been created.';
	}
	/**
     * Generates an application event from command line.
     *
     * @param  string $namespace
     * @param  string $event
     * @return string
     */
	public function generateEvent($namespace, $event)
	{
		$string = "<?php

namespace " . $namespace . ";

use Zefire\Contracts\Eventable;

class " . $event . " implements Eventable
{
	public function handle()
	{
		//
	}
}";
		$directory = \App::appPath() . str_replace('\\', DIRECTORY_SEPARATOR, str_replace('App\\', '', $namespace));
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0755, true)) {
    			throw new \Exception('Failed to create directory: ' . $directory);
			}
		}
		\File::put($directory . DIRECTORY_SEPARATOR . $event . '.php', $string);
		return $namespace . '\\' .  $event . ' event has been created.';
	}
	/**
     * Generates an application model from command line.
     *
     * @param  string $namespace
     * @param  string $model
     * @param  string $table
     * @param  string $connection
     * @return string
     */
	public function generateModel($namespace, $model, $table, $connection = '')
	{
		$string = "<?php

namespace " . $namespace . ";

use Zefire\Database\Model;

class " . $model . " extends Model
{
    protected \$connection = '" . $connection . "';

    protected \$table = '" . $table . "';

    protected \$model = '" . $namespace . '\\' . $model . "';

    public static function boot()
    {
        \$model = get_class();
        return new $model();
    }
}";
		$directory = \App::appPath() . str_replace('\\', DIRECTORY_SEPARATOR, str_replace('App\\', '', $namespace));
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0755, true)) {
    			throw new \Exception('Failed to create directory: ' . $directory);
			}
		}
		\File::put($directory . DIRECTORY_SEPARATOR . $model . '.php', $string);
		return $namespace . '\\' .  $model . ' model has been created.';
	}
	/**
     * Generates the default authentication and
     * registration controllers and views from command line.
     *
     * @return string
     */
	public function generateAuth()
	{
		if (!file_exists(\App::appPath() . '/Controllers/Http/Auth')) {
			if (!mkdir(\App::appPath() . '/Controllers/Http/Auth', 0755, true)) {
    			throw new \Exception('Failed to create Auth directory');
			}
		}
		$string = "<?php

namespace App\Controllers\Http\Auth;

use Zefire\Controllers\Auth\Authenticate as Auth;

class Authenticate extends Auth
{
	//	
}";
		\File::put(\App::appPath() . '/Controllers/Http/Auth/Authenticate.php', $string);
		$string = "<?php

namespace App\Controllers\Http\Auth;

use Zefire\Controllers\Auth\Registration;

class Register extends Registration
{
	//
}";
		\File::put(\App::appPath() . '/Controllers/Http/Auth/Register.php', $string);
		if (!file_exists(\App::resourcesPath() . '/templates/auth')) {
			if (!mkdir(\App::resourcesPath() . '/templates/auth', 0755, true)) {
    			throw new \Exception('Failed to create auth directory');
			}
		}
		$email = "{{ translate('auth.email') }}";
		$password = "{{ translate('auth.password') }}";
		$string = "@extends('layout.master')

@section('content')
    <div class=\"content\">
        <form action=\"/auth/login\" method=\"POST\">
        	@csrf
            <p><label for=\"email\"><b>{{ translate('auth.email') }}</b></label></p>
            <p><input type=\"text\" placeholder=\"" . $email . "\" name=\"email\" required></p>

            <p><label for=\"password\"><b>{{ translate('auth.password') }}</b></label></p>
            <p><input type=\"password\" placeholder=\"" . $password . "\" name=\"password\" required></p>

            <p><button type=\"submit\">{{ translate('auth.login') }}</button></p>
        </form>
    </div>
@endsection";
		\File::put(\App::resourcesPath() . '/templates/auth/login.php', $string);
		$string = "@extends('layout.master')

@section('content')
    <div class=\"content\">
        <form action=\"/auth/register\" method=\"POST\">
        	@csrf
            <p><label for=\"email\"><b>{{ translate('auth.email') }}</b></label></p>
            <p><input type=\"text\" placeholder=\"" . $email . "\" name=\"email\" required></p>

            <p><label for=\"password\"><b>{{ translate('auth.password') }}</b></label></p>
            <p><input type=\"password\" placeholder=\"" . $password . "\" name=\"password\" required></p>

            <p><button type=\"submit\">{{ translate('auth.register') }}</button></p>
        </form>
    </div>
@endsection";
		\File::put(\App::resourcesPath() . '/templates/auth/register.php', $string);
		$string = "<?php

return [
	'login' 		=> 'Login',
	'register' 		=> 'Register',
	'logout' 		=> 'Logout',
	'email' 		=> 'Email',
	'password' 		=> 'Password',
];";
		\File::put(\App::resourcesPath() . '/lang/en/auth.php', $string);
		return 'Authentication controllers have been created.';
	}
	/**
     * Generates an application command from command line.
     *
     * @param  string $command
     * @param  string $namespace
     * @param  string $controller
     * @return string
     */
	public function generateCommand($command, $namespace, $controller)
	{
		$string = "<?php

namespace " . $namespace . ";

class " . $controller . "
{
	public function " . camel_case($command) . "()
	{
		//
	}
}";
		$directory = \App::appPath() . str_replace('\\', DIRECTORY_SEPARATOR, str_replace('App\\', '', $namespace));
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0755, true)) {
    			throw new \Exception('Failed to create directory: ' . $directory);
			}
		}
		\File::put($directory . DIRECTORY_SEPARATOR . $controller . '.php', $string);
		return $namespace . '\\' .  $command . ' command has been created.';
	}
	/**
     * Generates a list of application's routes or
     * commands from command line.
     *
     * @param  string $type
     * @return string
     */
	public function listRoutes($type = 'web')
	{
		if ($type == 'web') {
			include_once(\App::routingPath() . 'Routes.php');	
			$routes = \Route::getRoutes();
		} else if ($type == 'console') {
			include_once(\App::routingPath() . 'Commands.php');
			$routes = \Command::getCommands();
		} else {
			$routes = 'No routes found';
		}
		dd($routes);
	}
	/**
     * Generates the application key from command line.
     *
     * @return string
     */
	public function key()
	{
		dd(\Hasher::make(\App::host() . uniqid(rand(), true)));
	}
	/**
     * Deletes files from a specific disk.
     *
     * @param  string $disk
     * @return string
     */
	protected function clearFiles($disk)
	{
		$files = \FileSystem::disk($disk)->list();
		foreach ($files as $file) {
			if (!in_array($file, ['.', '..', '.DS_Store'])) {
				\FileSystem::disk($disk)->delete($file);
			}
		}
	}
}