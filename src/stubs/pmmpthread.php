<?php
namespace pmmp\thread;
/**
 * pmmpthread
 */
class ConnectionException extends \RuntimeException{

}

class NonThreadSafeValueError extends \ValueError{

}

class Pool{
	/**
	 * The maximum number of Worker threads allowed in this Pool
	 *
	 * @var int
	 */
	protected $size;

	/**
	 * The name of the Worker class for this Pool
	 *
	 * @var string
	 */
	protected $class;

	/**
	 * The array of Worker threads for this Pool
	 *
	 * @var Worker[]
	 */
	protected $workers;

	/**
	 * The constructor arguments to be passed by this Pool to new Workers upon construction
	 *
	 * @var array
	 */
	protected $ctor;

	/**
	 * The numeric identifier for the last Worker used by this Pool
	 *
	 * @var int
	 */
	protected $last = 0;

	/**
	 * Construct a new Pool of Workers
	 *
	 * @param integer $size The maximum number of Workers this Pool can create
	 * @param string $class The class for new Workers
	 * @param array $ctor An array of arguments to be passed to new Workers
	 */
	public function __construct(int $size, string $class = Worker::class, array $ctor = []) {}

	/**
	 * Collect references to completed tasks
	 *
	 * Allows the Pool to collect references determined to be garbage by the given collector
	 *
	 * @param callable|null $collector
	 * @return int the number of tasks collected from the pool
	 */
	public function collect(callable $collector = null) : int{}

	/**
	 * Resize the Pool
	 *
	 * @param integer $size The maximum number of Workers this Pool can create
	 */
	public function resize(int $size) : void{}

	/**
	 * Shutdown all Workers in this Pool
	 */
	public function shutdown() : void{}

	/**
	 * Submit the task to the next Worker in the Pool
	 *
	 * @param Runnable $task The task for execution
	 *
	 * @return int the identifier of the Worker executing the object
	 */
	public function submit(Runnable $task) : int{}

	/**
	 * Submit the task to the specific Worker in the Pool
	 *
	 * @param int $worker The worker for execution
	 * @param Runnable $task The task for execution
	 *
	 * @return int the identifier of the Worker that accepted the object
	 */
	public function submitTo(int $worker, Runnable $task) : int{}
}

abstract class Runnable extends ThreadSafe
{
	/**
	 * Tell if the referenced object is executing
	 *
	 * @return bool A boolean indication of state
	 */
	public function isRunning() : bool{}

	/**
	 * Tell if the referenced object exited, suffered fatal errors, or threw uncaught exceptions during execution
	 *
	 * @return bool A boolean indication of state
	 */
	public function isTerminated() : bool{}

	/**
	 * The programmer should always implement the run method for objects that are intended for execution.
	 *
	 * @return void The methods return value, if used, will be ignored
	 */
	abstract public function run() : void;
}

abstract class Thread extends Runnable
{
	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_NONE
	 *
	 * The newly created thread will inherit nothing from its parent, as if starting a new request.
	 */
	public const INHERIT_NONE = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_INI
	 *
	 * The newly created thread will inherit INI overrides set by ini_set() in the parent thread(s).
	 * If not set, the settings defined in php.ini will be used.
	 */
	public const INHERIT_INI = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_CONSTANTS
	 *
	 * The newly created thread will copy all global constants with simple or thread-safe values from its parent.
	 * Note: Constants containing non-thread-safe objects or resources cannot be copied.
	 *
	 * Do not rely on this for production. Prefer relying on autoloading instead (e.g. Composer
	 * bootstrap files), which is more reliable (and takes less memory, when OPcache is used).
	 */
	public const INHERIT_CONSTANTS = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_FUNCTIONS
	 *
	 * The newly created thread will copy all global functions from its parent.
	 *
	 * Do not rely on this for production. Prefer relying on autoloading instead (e.g. Composer
	 * bootstrap files), which is more reliable (and takes less memory, when OPcache is used).
	 */
	public const INHERIT_FUNCTIONS = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_CLASSES
	 *
	 * The newly created thread will copy all classes from its parent.
	 *
	 * !!!!! WARNING !!!!! This has a significant performance cost in large applications with many
	 * classes. Avoid relying on this. Prefer relying on autoloading instead (e.g. Composer
	 * bootstrap files), which is more reliable (and takes less memory, when OPcache is used).
	 *
	 * Note: Disabling this flag only prevents class copying during thread start. Classes may still
	 * be copied at other times, such as when a new thread is started, since no autoloader would be
	 * present inside the new thread to load the thread's own class.
	 */
	public const INHERIT_CLASSES = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_INCLUDES
	 *
	 * The newly created thread will copy the list of included and required files from its parent.
	 */
	public const INHERIT_INCLUDES = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_COMMENTS
	 *
	 * The newly created thread will copy doc comments of any classes, functions or constants that
	 * it inherits from its parent.
	 */
	public const INHERIT_COMMENTS = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_INHERIT_ALL
	 *
	 * Everything (classes, functions, constants, includes, doc comments, ini settings) will be
	 * copied from the parent thread.
	 *
	 * !!!!! WARNING !!!!! This has a significant performance cost in large applications with many
	 * classes. Avoid relying on this. Prefer relying on autoloading instead (e.g. Composer
	 * bootstrap files), which is more reliable (and takes less memory, when OPcache is used).
	 */
	public const INHERIT_ALL = UNKNOWN;

	/**
	 * @var int
	 * @cvalue PMMPTHREAD_ALLOW_HEADERS
	 *
	 * Allows the new thread to emit HTTP headers.
	 */
	public const ALLOW_HEADERS = UNKNOWN;

	/**
	 * Will return the identity of the Thread that created the referenced Thread
	 *
	 * @return int A numeric identity
	 */
	public function getCreatorId() : int{}

	/**
	 * Will return the instance of currently executing thread
	 *
	 * @return Thread|null
	 */
	public static function getCurrentThread() : ?Thread{}

	/**
	 * Will return the identity of the currently executing thread
	 *
	 * @return int
	 */
	public static function getCurrentThreadId() : int{}

	/**
	 * Returns a ThreadSafeArray of globals accessible to all threads
	 * Any modification made will be seen by all threads
	 *
	 * @return ThreadSafeArray
	 */
	public static function getSharedGlobals() : ThreadSafeArray{}

	/**
	 * Returns the total number of Threads and Workers which have been
	 * started but not yet successfully joined/shutdown.
	 *
	 * The following are **not** included:
	 * - Threads which have been created but not started
	 * - Threads which have already been joined/shutdown
	 * - Threads which are not managed by pmmpthread (e.g. created by other extensions)
	 * - The main process thread
	 *
	 * @return int
	 */
	public static function getRunningCount() : int{}

	/**
	 * Will return the identity of the referenced Thread
	 *
	 * @return int
	 */
	public function getThreadId() : int{}

	/**
	 * Tell if the referenced Thread has been joined by another context
	 *
	 * @return bool A boolean indication of state
	 */
	public function isJoined() : bool{}

	/**
	 * Tell if the referenced Thread has been started
	 *
	 * @return bool A boolean indication of state
	 */
	public function isStarted() : bool{}

	/**
	 * Causes the calling context to wait for the referenced Thread to finish executing
	 *
	 * @return bool A boolean indication of state
	 */
	public function join() : bool{}

	/**
	 * Will start a new Thread to execute the implemented run method
	 *
	 * @param int $options An optional mask of inheritance constants, by default INHERIT_ALL
	 *
	 * @return bool A boolean indication of success
	 */
	public function start(int $options) : bool{}
}

class ThreadSafe implements \IteratorAggregate
{
	/**
	 * Send notification to the referenced object
	 *
	 * @return bool A boolean indication of success
	 */
	public function notify() : bool{}

	/**
	 * Send notification to one context waiting on the ThreadSafe
	 *
	 * @return bool A boolean indication of success
	 */
	public function notifyOne() : bool{}

	/**
	 * Executes the block while retaining the synchronization lock for the current context.
	 *
	 * @param \Closure $function The block of code to execute
	 * @param mixed $args... Variable length list of arguments to use as function arguments to the block
	 *
	 * @return mixed The return value from the block
	 */
	public function synchronized(\Closure $function, mixed ...$args) : mixed{}

	/**
	 * Waits for notification from the Stackable
	 *
	 * @param int $timeout An optional timeout in microseconds
	 *
	 * @return bool A boolean indication of success
	 */
	public function wait(int $timeout = 0) : bool{}

	public function getIterator() : \Iterator{}
}

final class ThreadSafeArray extends ThreadSafe implements \Countable, \ArrayAccess
{
	/**
	 * Removes and returns $size items from the array. Equivalent to calling shift() $size times.
	 * Similar to array_slice() with offset 0, but also removes the items returned.
	 *
	 * @param int $size The number of items to fetch
	 * @param bool $preserve Preserve the keys of members
	 *
	 * @return array An array of items removed from the array
	 */
	public function chunk(int $size, bool $preserve = false) : array{}

	/**
	 * {@inheritdoc}
	 */
	public function count() : int{}

	/**
	 * Converts the given array into a ThreadSafeArray object (recursively)
	 * @param array $array
	 *
	 * @return ThreadSafeArray A ThreadSafeArray object created from the provided array
	 * @throws NonThreadSafeValueError if the array contains any non-thread-safe values
	 */
	public static function fromArray(array $array) : ThreadSafeArray{}

	/**
	 * Merges data into the current ThreadSafeArray, similar to array_merge()
	 *
	 * @param array|object $from The data to merge
	 * @param bool $overwrite Overwrite existing keys flag
	 *
	 * @return bool A boolean indication of success
	 * @throws NonThreadSafeValueError if $from contains any non-thread-safe values
	 */
	public function merge(array|object $from, bool $overwrite = true) : bool{}

	/**
	 * Pops an item from the end of the array, similar to array_pop()
	 *
	 * @return mixed The last item in the array
	 */
	public function pop() : mixed{}

	/**
	 * Shifts an item from the start of the array, similar to array_shift()
	 *
	 * @return mixed The first item in the array
	 */
	public function shift() : mixed{}

	public function offsetGet(mixed $offset) : mixed{}

	public function offsetSet(mixed $offset, mixed $value) : void{}

	public function offsetExists(mixed $offset) : bool{}

	public function offsetUnset(mixed $offset) : void{}
}

class Worker extends Thread
{
	/**
	 * Executes the optional collector on each of the tasks, removing the task if true is returned
	 *
	 * @param callable $function The collector to be executed upon each task
	 * @return int The number of tasks left to be collected
	 */
	public function collect(callable $function = null) : int{}

	/**
	 * Default collection function called by collect(), if a collect callback wasn't given.
	 *
	 * @param Runnable $collectable The collectable object to run the collector on
	 * @return bool Whether or not the object can be disposed of
	 */
	public function collector(Runnable $collectable) : bool{}

	/**
	 * Returns the number of threaded tasks waiting to be executed by the referenced Worker
	 *
	 * @return int An integral value
	 */
	public function getStacked() : int{}

	/**
	 * Tell if the referenced Worker has been shutdown
	 *
	 * @return bool A boolean indication of state
	 * @alias pmmp\thread\Thread::isJoined
	 */
	public function isShutdown() : bool{}

	/**
	 * Shuts down the Worker after executing all the threaded tasks previously stacked
	 *
	 * @return bool A boolean indication of success
	 * @alias pmmp\thread\Thread::join
	 */
	public function shutdown() : bool{}

	/**
	 * Appends the referenced object to the stack of the referenced Worker
	 *
	 * @param Runnable $work object to be executed by the referenced Worker
	 *
	 * @return int The new length of the stack
	 */
	public function stack(Runnable $work) : int{}

	/**
	 * Removes the first task (the oldest one) in the stack.
	 *
	 * @return Runnable|null The item removed from the stack
	 */
	public function unstack() : ?Runnable{}

	/**
	 * Performs initialization actions when the Worker is started.
	 * Override this to do actions on Worker start; an empty default implementation is provided.
	 *
	 * @return void
	 */
	public function run() : void{}
}