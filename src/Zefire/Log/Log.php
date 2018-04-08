<?php
namespace Zefire\Log;

use Zefire\FileSystem\FileSystem;

class Log
{
    /**
     * Stores a FileSystem instance.
     *
     * @var Zefire\FileSystem\FileSystem
     */
    protected $fileSystem;
    /**
     * Stores log files paths.
     *
     * @var array
     */
    protected $logFile = [
        'error' => 'error.log',
        'app'   => 'app.log',
        'db'    => 'db.log',
        'queue' => 'queue.log'
    ];
    /**
     * Creates a new log instance.
     *
     * @param  Zefire\FileSystem\FileSystem $fileSystem
     * @return void
     */
    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
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
        if ($this->fileSystem->disk('logs')->exists($this->logFile[$type])) {
            $this->fileSystem->disk('logs')->append($this->logFile[$type], date("Y-m-d H:i:s") . ': ' . $logEntry . "\n");
        } else {
            $this->fileSystem->disk('logs')->put($this->logFile[$type], date("Y-m-d H:i:s") . ': ' . $logEntry . "\n");
        }        
    }
}