<?php

namespace Zefire\Session;

use Zefire\FileSystem\File;

class FileSessionHandler implements \SessionHandlerInterface
{
	/**
     * Stores a file instance.
     *
     * @var \Zefire\FileSystem\File
     */
    protected $file;
    /**
     * Stores the session path.
     *
     * @var string
     */
	protected $path;
    /**
     * Stores the session id.
     *
     * @var string
     */
    protected $id;
    /**
     * Creates a new file session handler instance.
     *
     * @return void
     */
	public function __construct(File $file)
    {
        $this->file = $file;        
    }
    /**
     * Open session save handler callback.
     *
     * @param  string $savePath
     * @param  string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        $this->path = $savePath;
        return true;
    }
    /**
     * Close session save handler callback.
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }
    /**
     * Read session save handler callback.
     *
     * @param  string $sessionId
     * @return mixed
     */
    public function read($sessionId)
    {
        $this->id = $sessionId;
        $file = $this->path . '/' . $sessionId;
        if (!$this->file->exists($file)) {
            $this->file->put($file, $contents = '');
        }
        return $this->file->get($file, true);
    }
    /**
     * Write session save handler callback.
     *
     * @param  string $sessionId
     * @param  mixed  $data
     * @return bool
     */
    public function write($sessionId, $data)
    {
        $this->file->put($this->path . '/' . $sessionId, $data, true);
        return true;
    }
    /**
     * Destroy session save handler callback.
     *
     * @param  string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        $this->file->delete($this->path . '/' . $sessionId);
        return true;
    }
    /**
     * Garbage collection session save handler callback.
     *
     * @param  int $lifetime
     * @return void
     */
    public function gc($lifetime)
    {
        $file = $this->file->get($this->path . '/' . $this->id);
        if (filemtime($file) + \App::config('session.life') < time() && file_exists($file)) {
            unlink($file);
        }
    }
}