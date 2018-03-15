<?php

namespace Zefire\Exception;

class HttpException
{
	/**
     * Generates an Http exception.
     *
     * @param  int    $code
     * @param  string $message
     * @throws \exception
     */
	public function abort($code, $message = '')
	{
		if ($message == '') {
			$message = \App::config('http.' . $code);
		}
		throw new \Exception($message, $code);
	}
}