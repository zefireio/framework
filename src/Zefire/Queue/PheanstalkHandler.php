<?php

namespace Zefire\Queue;

use Pheanstalk\Pheanstalk;
use Zefire\Core\Serializable;

class PheanstalkHandler
{
	use Serializable;
    /**
     * Stores pheanstalk client connection.
     *
     * @var \Pheanstalk\Pheanstalk
     */
    protected $client;
    /**
     * Creates a new pheanstalk handler instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connect();
    }
    /**
     * Connects to Pheanstalk.
     *
     * @return void
     */
    public function connect()
    {
        $this->client = new Pheanstalk(\App::config('queueing.beanstalk'));
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
        return $this->client->useTube($queue)->put($job);
    }
    /**
     * Listens for jobs released on a queue.
     *
     * @param  string $queue
     * @return void
     */
    public function listen($queue)
    {
        $this->client->watch($queue);
        while ($job = $this->client->reserve()) {
            $data = json_decode($job->getData());
            $delay = $data->delay - time();
            if ($delay > 0) {
                $this->client->release($job, 1024, $delay);
                \Dispatcher::now('queue-push', ['queue' => $queue, 'job' => $job]);
			} else {
                $attempts = 0;
                do {                    
                    $status = $this->process($data);                    
                    if ($status) {
                        $this->client->delete($job);
                        \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 1]);
                        break;
                    } else {
                        $attempts++;
                        \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 2]);
                    }                    
                } while($attempts < $data->tries);
                if ($attempts == $data->tries) {
                    $this->client->bury($job);
                    \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 3]);
                }
			}			
        }
    }
    /**
     * Clears all jobs on a queue.
     *
     * @param  string $tube
     * @return string
     */
    public function clearQueue($tube = 'default')
    {   
        if ($this->client->statsTube($tube)->current_jobs_ready == 0) {
            return 'Nothing in the queue.';
        } else {
            $count = 0;
            while($job = $this->client->peekReady($tube)) {
                $this->client->delete($job);
                $count++;
            }
            return 'Cleared ' . $count . ' jobs from queue.';
        }
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