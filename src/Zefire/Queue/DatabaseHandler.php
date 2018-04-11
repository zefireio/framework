<?php

namespace Zefire\Queue;

class DatabaseHandler
{
    /**
     * Pushes a job on a queue.
     *
     * @param  mixed  $job
     * @param  string $queue
     * @return int
     */
    public function push($job, $queue)
    {
        return \DB::connection('mysql1')->table('queued_job')->insert(['queue' => $queue, 'job' => $job]);
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
            $job = \DB::connection('mysql1')->table('queued_job')->select(['id', 'job'])->where('queue', '=', $queue)->first();
            if ($job != null && $job !== false) {
                $id = $job->id;
                $data = json_decode($job->job);
                $delay = $data->delay - time();
                if ($delay > 0) {
                    $this->push($queue, json_encode($data));
                    \Dispatcher::now('queue-push', ['queue' => $queue, 'job' => $job]);
                } else {
                    $attempts = 0;
                    do {                    
                        $status = $this->process($data);                    
                        if ($status) {
                            \DB::connection('mysql1')->table('queued_job')->where('id', '=', $id)->forceDelete();
                            \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 1]);
                            break;
                        } else {
                            $attempts++;
                            \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 2]);
                        }                    
                    } while($attempts < $data->tries);
                    if ($attempts == $data->tries) {
                        \DB::connection('mysql1')->table('queued_job')->where('id', '=', $id)->forceDelete();
                        $this->push('failed_job', json_encode($data));
                        \Dispatcher::now('queue-job-status', ['queue' => $queue, 'job' => $job, 'status' => 3]);                        
                    }
                }
            }
            sleep(\App::config('queueing.wait'));			
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
        \DB::connection('mysql1')->table('queued_job')->where('queue', '=', $queue)->forceDelete();
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
    /**
     * Checks if the queue tables exists
     * and will create them if needed.
     *
     * @return void
     */
    protected function checkQueueTables()
    {
        \DB::connection('mysql1')->raw("CREATE TABLE IF NOT EXISTS `queued_job` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_by` int(11) DEFAULT NULL,
            `updated_by` int(11) DEFAULT NULL,
            `deleted_by` int(11) DEFAULT NULL,
            `queue` varchar(255) DEFAULT NULL,
            `job` longtext DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;"
        );
        \DB::connection('mysql1')->raw("CREATE TABLE IF NOT EXISTS `failed_job` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_by` int(11) DEFAULT NULL,
            `updated_by` int(11) DEFAULT NULL,
            `deleted_by` int(11) DEFAULT NULL,
            `queue` varchar(255) DEFAULT NULL,
            `job` longtext DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;"
        );
    }
}