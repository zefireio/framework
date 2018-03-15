<?php

namespace Zefire\Console;

class Response
{
	/**
     * Stores the response content.
     *
     * @var string
     */
	protected $content;
	/**
     * Create a new console response instance.
     *
     * @param. string $content
     * @return void
     */
	public function __construct($content = '')
    {
    	$this->setContent($content);
    }
    /**
     * Sets the content.
     *
     * @return void
     */
	public function setContent($content)
	{
		$this->content = $content;
	}
	/**
     * Sends the response to terminal.
     *
     * @return void
     */
	public function send()
	{
		dump($this->content);
	}
}