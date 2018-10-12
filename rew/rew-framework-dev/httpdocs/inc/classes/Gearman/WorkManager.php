<?php

/**
 * Manages The Workload Of A Worker As Well As Queue Tasks
 * @author David
 *
 */
class Gearman_WorkManager
{

    /**
     * Reference To Client Object
     * @var GearmanClient
     */
    private $client;

    /**
     * Maximum number of handles (tasks) that may be queued by the client
     * @var int
     */
    private $max_handles;

    /**
     * Maximum number of workers that may operate on a set of queued tasks at one time
     * @var int
     */
    private $max_workers;

    /**
     * File Path to the worker
     * @var string
     */
    private $worker_path = '';

    /**
     * Worker function's name
     * @var string
     */
    private $worker_name = '';

    /**
     * Additional arguments that may be passed to the worker script when called
     * @var string
     */
    private $worker_args = '';

    /**
     * Array of task IDs
     * @var array
     */
    private $handles = array();

    /**
     * Total number of tasks that will be completed.
     * @var int
     */
    private $total_tasks;

    /**
     * Total number of tasks that have been queued so far
     * @var int
     */
    private $tasks_queued;

    /**
     * Defines whether Gearman Workers Should Run In Debug Mode
     */
    private $debug_mode = false;

    /**
     * Constructor
     * @param GearmanClient $client
     * @param string $worker_path
     * @param string $worker_name
     * @param array $worker_args
     * @param int $max_handles
     * @param int $max_workers
     */
    public function __construct(GearmanClient &$client, $worker_path, $worker_name, $worker_args = array(), $max_handles = 5000, $max_workers = 2, $debug_mode = false)
    {
        $this->client = $client;

        $this->max_handles = $max_handles;

        $this->max_workers = $max_workers;

        $this->worker_path = escapeshellarg($worker_path);
        $this->worker_name = $worker_name;

        array_walk($worker_args, function (&$arg, $key) {
            $arg = escapeshellarg($arg);
        });

        $this->worker_args = $worker_args;

        $this->tasks_queued = 0;

        $this->debug_mode = !empty($debug_mode);
    }

    /**
     * Routine method that will start workers if the worker count is less than the set maximum number of workers
     */
    public function startWorkersAsNecessary()
    {
        exec('gearadmin --workers | grep ' . escapeshellarg($this->worker_name), $running_workers, $res);
        $actual_worker_count = count($running_workers);

        echo "Total workers running " . $actual_worker_count . PHP_EOL;

        // Determine Whether Workers Need To Be Started And Start As Necessary
        if ($actual_worker_count < $this->max_workers) {
            echo "More workers needed.  Starting workers." . PHP_EOL;

            for ($i = $actual_worker_count; $i < $this->max_workers; $i++) {
                exec('php ' . $this->worker_path . ' ' . (!empty($this->worker_args) ? implode(' ', $this->worker_args) : '') . (!empty($this->debug_mode) ? ' debug' : '') . ' > /dev/null 2>&1 &', $output, $error);

                if (!empty($error)) {
                    throw new Exception("Unable to start worker");
                }

                echo "Worker started" . PHP_EOL;
            }
        }
    }

    /**
     * Monitors the current queue of tasks until the tasks are complete.
     */
    public function monitorWorkerQueue()
    {

        if (empty($this->tasks_queued)) {
            $this->tasks_queued = $this->getHandleCount();
        }

        $done = false;
        do {
            sleep(2);

            $this->startWorkersAsNecessary();

            foreach ($this->handles as $key => $handle) {
                $stat = $this->client->jobStatus($handle);
                if (!$stat[0]) {
                    unset($this->handles[$key]);
                }
            }

            if (empty($this->handles)) {
                $done = true;
            }

            echo "Running: " . ((int) $this->tasks_queued - $this->getHandleCount()) . " of " . $this->total_tasks . PHP_EOL;
        } while (!$done);
    }

    /**
     * Returns the maximum number of tasks that may be queued.
     * @return number
     */
    public function getMaxHandles()
    {
        return $this->max_handles;
    }

    /**
     * Sets the maximum number of tasks that may be queued.
     * @param int $max_handles
     */
    public function setMaxHandles($max_handles)
    {
        $this->max_handles = $max_handles;
    }

    /**
     * Adds a handle (task id) to the handles array
     * @param string $handle
     */
    public function addHandle($handle)
    {
        $this->handles[] = $handle;
    }

    /**
     * Returns the current number of tasks yet to be completed.
     * @return number
     */
    public function getHandleCount()
    {
        return count($this->handles);
    }

    /**
     * Sets the total number of tasks to be completed.  Used for queue monitoring.
     * @param int $total_tasks
     */
    public function setTotalTasks($total_tasks)
    {
        $this->total_tasks = $total_tasks;
    }

    /**
     * Queues a task to be run in the background.
     * @param array $data
     * @throws Exception
     */
    public function runTaskInBackground($data = array())
    {

        $this->addHandle($this->client->doBackground($this->worker_name, serialize($data)));

        if ($this->client->returnCode() != GEARMAN_SUCCESS) {
            throw new Exception("Failed to create background process" . PHP_EOL);
        }

        // If the queue reaches 10k or more, wait for the queue to deplete before continuing
        if (count($this->handles) > $this->getMaxHandles()) {
            $this->tasks_queued += count($this->handles);

            $this->monitorWorkerQueue();
        }
    }
}
