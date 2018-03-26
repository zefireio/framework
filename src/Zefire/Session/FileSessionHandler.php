<?php

namespace Zefire\Session;

use Zefire\FileSystem\FileSystem;

class FileSessionHandler implements \SessionHandlerInterface
{
	/**
     * Stores a FileSystem instance.
     *
     * @var \Zefire\FileSystem\FileSystem
     */
    protected $fileSystem;
    /**
     * Creates a new file session handler instance.
     *
     * @return void
     */
	public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;        
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
        $this->fileSystem->disk('sessions')->put($sessionId, '');
        return $this->fileSystem->disk('sessions')->get($sessionId);
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
        $this->fileSystem->disk('sessions')->put($sessionId, $data);
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
        $this->fileSystem->disk('sessions')->delete($sessionId);
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
        $files = \FileSystem::disk('sessions')->list();
        foreach ($files as $file) {
            if (!in_array($file, ['.', '..', '.DS_Store'])) {
                if ((time() - $this->fileSystem->disk('compiled')->lastModified($file)) > \App::config('session.life')) {
                    $this->fileSystem->disk('sessions')->delete($filename . $this->extension);
                }
            }
        }
    }
}