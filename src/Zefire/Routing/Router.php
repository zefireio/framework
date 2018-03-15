<?php

namespace Zefire\Routing;

use Zefire\Http\Request as HttpRequest;
use Zefire\Http\Response as HttpResponse;
use Zefire\Console\Request as CliRequest;
use Zefire\Console\Response as CliResponse;
use Zefire\Routing\Transport;

class Router
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
	protected $request;
	/**
     * Stores matched route.
     *
     * @var \Zefire\Routing\Route
     */
	protected $route;
	/**
     * Stores matched command.
     *
     * @var \Zefire\Routing\Command
     */
	protected $command;
	/**
     * Handles a request.
     *
     * @return mixed
     */
	public function handle()
	{
		$this->maintenanceMode();
		if (\App::runningMode() == 'http') {
			$this->request = new HttpRequest();
			include_once(\App::routingPath() . 'Routes.php');
			$routes = \Route::getRoutes();
			$this->route = $this->match($routes);
			if ($this->route == null) {
				\HttpException::abort(404);
			} else {
				$transport = new Transport();
				return new HttpResponse(200, $transport->send($this->request)->through($this->route)->execute());
			}
		} else {
			$this->request = new CliRequest();
			include_once(\App::zefirePath() . 'Console' . DIRECTORY_SEPARATOR . 'Commands.php');
			include_once(\App::routingPath() . 'Commands.php');
			$commands = \Command::getCommands();
			$this->command = $this->match($commands);
			if ($this->command == null) {
				return new \Exception('Command not found');
			} else {
				$transport = new Transport();
				return new CliResponse($transport->send($this->request)->run($this->command));
			}			
		}		
	}
	/**
     * Matches the request's route (or command)
     * against registered routes (or commands).
     *
     * @param  object $data
     * @return mixed
     */
	protected function match($data)
	{
		if (\App::runningMode() == 'http') {
			$segments = $this->request->segments();
			$matches = [];
			foreach ($data[strtolower($this->request->method())] as $key => $value) {
				if (count($segments) == $value['segment_count']) {
					$matched = [];
					foreach ($value['segments'] as $k => $v) {
						if (!$this->isPlaceholder($v)) {
							if (in_array($v, $segments)) {
								array_push($matched, 1);							
							} else {
								array_push($matched, 0);
							}
						} else {
							array_push($matched, 1);
						}
					}
					if (!in_array(0, $matched)) {
						$matches[$key] = $value;	
					}				
				}			
			}
			switch (count($matches)) {
				case 0:
					return null;				
				case 1:
					reset($matches);
					$first_key = key($matches);
					return $matches[$first_key];				
				default:
					throw \Exception('No route could be matched');
			}
		} else {
			return (isset($data[$this->request->command()])) ? $data[$this->request->command()] : null;
		}		
	}
	/**
     * Checks if a route segment is a placeholder.
     *
     * @param  string $segment
     * @return int
     */
	protected function isPlaceholder($segment)
	{
		preg_match('/\{(.*?)\}/', $segment, $matches);
		return ($matches) ? 1 : 0;
	}
	/**
     * Checks if the application is in maintenance mode.
     * If it is then it will not process any route and
     * renders a maintenance view for Http routes.
     *
     * @return int
     */
	protected function maintenanceMode()
	{
		if (\App::maintenanceMode()) {
			if (\App::runningMode() == 'http') {
				echo $this->getMaintenanceHtml();
				exit;
			}
		}
	}
	/**
     * Generates the HTML maintenance mode view.
     *
     * @return string
     */
	protected function getMaintenanceHtml()
	{
		return "<!doctype html>
<html lang=\"en\">
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <title>Service down for maintenance</title>

        <!-- Fonts -->
        <link href=\"https://fonts.googleapis.com/css?family=Raleway:100,600\" rel=\"stylesheet\" type=\"text/css\">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }
            .full-height {
                height: 100vh;
            }
            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }
            .position-ref {
                position: relative;
            }
            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
            .content {
                text-align: center;
            }
            .title {
                font-size: 84px;
            }
            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class=\"flex-center position-ref full-height\">
            <div class='content'>
                <h1>Service down for maintenance</h1>  
                <p>Will be back up shortly...</p>
            </div>
        </div>
    </body>
</html>";
	}
}