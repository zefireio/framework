<?php

namespace Zefire\Exception;

use \Exception;
use \ErrorException;
use Zefire\Contracts\Throwable;
use Zefire\Filesystem\FileSystem;
use Zefire\Log\Log;
use Zefire\Http\Response as HttpResponse;
use Zefire\Console\Response as CliResponse;

class ExceptionHandler implements Throwable
{
	/**
     * Holds a list of http error code and relevant messages.
     *
     * @var array
     */
    protected static $httpCodes = [
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '102' => 'Processing',
        '103' => 'Early Hints',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '207' => 'Multi-Status',
        '208' => 'Already Reported',
        '226' => 'IM Used',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '307' => 'Temporary Redirect',
        '308' => 'Permanent Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Payload Too Large',
        '414' => 'URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '418' => 'I\'m a teapot',
        '421' => 'Misdirected Request',
        '422' => 'Unprocessable Entity',
        '423' => 'Locked',
        '424' => 'Failed Dependency',
        '425' => 'Reserved for WebDAV advanced collections expired proposal',
        '426' => 'Upgrade Required',
        '428' => 'Precondition Required',
        '429' => 'Too Many Requests',
        '431' => 'Request Header Fields Too Large',
        '451' => 'Unavailable For Legal Reasons',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
        '506' => 'Variant Also Negotiates',
        '507' => 'Insufficient Storage',
        '508' => 'Loop Detected',
        '510' => 'Not Extended',
        '511' => 'Network Authentication Required'
    ];
    /**
     * Holds a list of non valid http error codes.
     *
     * @var array
     */
    protected static $nonValidHttpCodes = [-1, 0, 1];
    /**
     * Handles exceptions.
     *
     * @param  \Exception $exception
     * @return void
     */
	public static function handleException($exception)
	{
		self::addLogEntry($exception);
        self::render($exception);
	}
    /**
     * Handles errors.
     *
     * @param  int    $level
     * @param  string $message
     * @param  string $file
     * @param  int    $line
     * @param  array  $context
     * @return void
     */
    public static function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
    	if (error_reporting() & $level) {
            self::handleException(new ErrorException($message, 0, $level, $file, $line));
        }
    }
    /**
     * Handles shutdowns.
     *
     * @return void
     */
    public static function handleShutdown()
    {
    	$error = error_get_last();
        if (!is_null($error) && self::isFatal($error['type'])) {
            $type = (isset($error['type']) && $error['type'] != '') ? $error['type'] : 0;
            $message = (isset($error['message']) && $error['message'] != '') ? $error['message'] : 'Unidentified error';
            $level = (isset($error['level']) && $error['level'] != '') ? $error['level'] : $type;
            $file = (isset($error['file']) && $error['file'] != '') ? $error['file'] : 'Could not identify filename';
            $line = (isset($error['line']) && $error['line'] != '') ? $error['line'] : 'Could not identify line';
            self::handleException(new ErrorException($message, 500, $level, $file, $line));
        }
    }
    /**
     * Add an entry to Zefire's log file.
     *
     * @param  \Exception $exception
     * @return void
     */
    protected static function addLogEntry($exception)
    {
        $logger = new Log(new FileSystem());
        $logger->push($exception);
    }
    /**
     * Renders an exception for cli and browsers.
     *
     * @param  \Exception $exception
     * @return void
     */
    protected static function render($exception)
    {
    	if (PHP_SAPI === 'cli') {
			$response = new CliResponse($exception);
    		$response->send();
		} else {
            $code = (in_array($exception->getCode(), self::$nonValidHttpCodes)) ? 500 : $exception->getCode();
			$data = [];
			$data['code'] = $code;
			$data['status'] = \App::config('http.' . $code);
			$data['message'] = $exception->getMessage();
			$data['file'] = $exception->getFile();
			$data['line'] = $exception->getLine();
			$data['trace'] = $exception->getTrace();
            $response = new HttpResponse($code, \View::render('errors.error', $data));
			$response->send();			
		}
    }
    /**
     * Checks if an error is fatal.
     *
     * @param  string $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }
}