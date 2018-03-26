<?php
namespace Zefire\Translation;

use Zefire\Session\Session;
use Zefire\Helpers\Arr;

class Translate
{
    /**
     * Stores the session instance.
     *
     * @var \Zefire\Session\Session
     */
    protected $session;
    /**
     * Creates a new translate instance.
     *
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    /**
     * Gets a translation using dot notation
     * from a local based translation file.
     *
     * @param  string $key
     * @return string
     */
    public function get($key)
    {
        $locale = ($this->session->exists('locale.code')) ? $this->session->get('locale.code') : \App::config('app.default_lang');
        $split = explode('.', $this->strip($key));
        $array = include \App::translatePath() . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $split[0] . '.php';
        unset($split[0]);
        $key = implode('.', $split);
        $value = Arr::get($key, $array);
        return ($value != null) ? $value : $key;
    }
    /**
     * Strips any single quote from a path.
     *
     * @param  string $path
     * @return string
     */
    protected function strip($path)
    {
        return str_replace("'", "", $path);
    }
}