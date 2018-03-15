<?php
namespace Zefire\Log;

use Zefire\FileSystem\File;

class Log
{
    /**
     * Stores a file instance.
     *
     * @var Zefire\FileSystem\File
     */
    protected $file;
    /**
     * Stores log files paths.
     *
     * @var array
     */
    protected $logFile = [];
    /**
     * Creates a new log instance.
     *
     * @param  Zefire\FileSystem\File $file
     * @return void
     */
    public function __construct(File $file)
    {
        $this->file = $file;
        $this->logFile['error'] = \App::logPath() . 'error.log';
        $this->logFile['app'] = \App::logPath() . 'app.log';
        $this->logFile['db'] = \App::logPath() . 'db.log';
    }
    /**
     * Add a new entry to log file.
     *
     * @param  string $logEntry
     * @param  string $type
     * @return void
     */
    public function push($logEntry, $type = 'error')
    {
        if ($this->file->exists($this->logFile[$type]) && $this->file->isWritable($this->logFile[$type])) {
            $this->file->append($this->logFile[$type], date("Y-m-d H:i:s") . ': ' . $logEntry . "\n");
        } else {
            $this->file->put($this->logFile[$type], date("Y-m-d H:i:s") . ': ' . $logEntry . "\n");
        }
    }
}