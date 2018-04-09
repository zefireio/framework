<?php

namespace Zefire\Queue;

use Zefire\Redis\Redis;
use Zefire\Core\Serializable;

class RedisHandler
{
	use Serializable;
    /**
     * Stores a redis client connection.
     *
     * @var \Redis
     */
    protected $client;
    /**
     * Creates a new redis handler instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connect();
    }
    /**
     * Connects to Redis.
     *
     * @return void
     */
    public function connect()
    {
        $this->client = new Redis('queueing');
    }
    /**
     * Pushes a job on a queue.
     *
     * @param  mixed  $job
     * @param  string $queue
     * @return int
     */
    public function push($job, $queue)
    {
        return $this->client->push($queue, $job);
    }
    /**
     * Listens for jobs released on a queue.
     *
     * @param  string $queue
     * @return void
     */
    public function listen($queue)
    {
        while (true) {
            $job = $this->client->pop($queue, $queue . '-processing', \App::config('queueing.wait'));
            if ($job != null) {
                $data = $this->client->parseJob($job);
                $delay = $data->delay - time();
                if ($delay > 0) {
                    $this->client->push($queue, json_encode($data));
                    \Dispatcher::now('queue-push', ['queue' => $queue, 'job' => $job]);
                } else {
                    $attempts = 0;
                    do {                    
                        $status = $this->process($data);                    
                        if ($status) {
                            $this->client->success($queue, json_encode($data));
                            \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 1]);
                            break;
                        } else {
                            $attempts++;
                            \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 2]);
                        }                    
                    } while($attempts < $data->tries);
                    if ($attempts == $data->tries) {
                        \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 3]);
                        $this->client->failed($queue . '-failed', json_encode($data));
                    }
                }
            }			
        }
    }
    /**
     * Clears all jobs on a queue.
     *
     * @param  string $queue
     * @return string
     */
    public function clearQueue($queue = 'default')
    {   
        $this->redis->flushQueue($queue);
    }
    /**
     * Processes a job released on a queue.
     *
     * @param  string $job
     * @return mixed
     */
    protected function process($job)
    {
        try {
            $args = json_decode($job->args, true);
            \Dispatcher::now('queue-process', ['job' => $job->name]);
            $job = \Factory::make($job->name);
            $dependencies = \App::resolveMethodDependencies($job, 'handle');
            $dependencies = array_merge($args, $dependencies);
            call_user_func_array(array($job, 'handle'), $dependencies);
            return true;
        } catch (\Exception $e) {
            return false;
        }        
    }
}