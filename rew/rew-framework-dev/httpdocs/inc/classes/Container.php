<?php

use REW\Core\Interfaces\ProviderInterface;
use REW\Core\Interfaces\ContainerInterface;

use DI\ContainerBuilder;
use DI\FactoryInterface;
use DI\DependencyException;
use Invoker\Exception\InvocationException;
use Invoker\Exception\NotCallableException;
use Interop\Container\Exception\NotFoundException;
use Interop\Container\Exception\ContainerException;
use Invoker\Exception\NotEnoughParametersException;
use Interop\Container\ContainerInterface as InteropContainerInterface;

use function DI\object as di_object;

class Container implements ContainerInterface
{
    /**
     * @const key representing import files
     */
    const IMPORT_CONFIG = 'config.imports';

    /**
     * @var Container
     */
    private static $instance;

    /**
     * @var InteropContainerInterface
     */
    private $container;

    /**
     * @var SplFixedArray
     */
    private $providerInstances;

    /**
     * @var SplStack
     */
    private $registeredProviderInstances;

    /**
     * @var bool
     */
    private $registeringProviders = false;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $builder = new ContainerBuilder;
        $this->container = $builder->build();
        $this->set(self::class, $this);
        $this->set(FactoryInterface::class, $this);
        $this->set(ContainerInterface::class, $this);
        $this->set(InteropContainerInterface::class, $this);
    }

    /**
     * Applies all bindings and registers/boots all service providers
     * @param array|null $configImports
     * @param array|null $bindings
     * @param array|null $providers
     * @throws Exception
     */
    public function boot(array $configImports = null, array $bindings = null, array $providers = null)
    {
        if ($configImports) {
            $this->container->set('config.imports', $configImports);
        }

        if ($bindings) {
            foreach ($bindings as $abstract => $concrete) {
                $this->set($abstract, $concrete);
            }
        }

        if ($providers) {
            $this->registerProviders($providers);
            $this->bootRegisteredProviders();
        }
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     * @throws Exception Error occurred resolving a service provider
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        // If we're registering providers, we may need to load a dependency
        if ($this->registeringProviders && !$this->container->has($id)) {
            $continue = true;

            // Pick up wherever registration left off... No sense rewinding and looking at things it has already
            // looked at.
            $this->providerInstances->rewind();
            while ($continue && $this->providerInstances->valid()) {
                /** @var ProviderInterface $provider */
                $provider = $this->providerInstances->current();

                // If we found a provider, we'll register it and then rewind as we need to make sure we go through
                // all elements. If not, we'll throw an exception after checking all providers.
                if ($provider !== null && in_array($id, $provider->provides())) {
                    $this->providerInstances[$this->providerInstances->key()] = null;
                    $provider->register();
                    $continue = false;
                    $this->providerInstances->rewind();
                }

                if ($continue) {
                    $this->providerInstances->next();
                }
            }

            if ($continue) {
                throw new Exception(sprintf('Could not find a service provider providing %s', $id));
            }
        }

        return $this->container->get($id);
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $name  Entry name
     * @param mixed $value Value, use definition helpers to define objects
     */
    public function set($name, $value)
    {
        if (is_string($value)) {
            $value = di_object($value);
        }
        $this->container->set($name, $value);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Call the given function using the given parameters.
     *
     * @param callable $callable   Function to call.
     * @param array    $parameters Parameters to use.
     *
     * @return mixed Result of the function.
     *
     * @throws InvocationException Base exception class for all the sub-exceptions below.
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function call($callable, array $parameters = array())
    {
        return $this->container->call($callable, $parameters);
    }

    /**
     * Resolves an entry by its name. If given a class name, it will return a new instance of that class.
     *
     * @param string $name       Entry name or a class name.
     * @param array  $parameters Optional parameters to use to build the entry. Use this to force specific
     *                           parameters to specific values. Parameters not defined in this array will
     *                           be automatically resolved.
     *
     * @throws \InvalidArgumentException The name parameter must be of type string.
     * @throws DependencyException       Error while resolving the entry.
     * @throws NotFoundException         No entry or class found for the given name.
     * @return mixed
     */
    public function make($name, array $parameters = [])
    {
        return $this->container->make($name, $parameters);
    }

    /**
     * Calls the parent constructor with bound dependencies.
     * Note that using this method isn't really the best design choice, and only exists to try to make Skin
     * relationships less fragile. Please don't use on new classes.
     * @param object $object The object we are working with
     * @param string $constructorClassName The class to call the constructor in (typically parent::class)
     * @param array $parameters
     * @throws InvalidArgumentException If $object isn't an object or doesn't inherit $constructorClassName
     * @throws DependencyException if an argument could not be resolved
     */
    public function callConstructor($object, $constructorClassName, $parameters = array())
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('$object must be an object.');
        }
        if (!is_subclass_of($object, $constructorClassName)) {
            throw new InvalidArgumentException('$object must inherit $constructorClassName.');
        }

        /** @var ReflectionClass $reflector */
        $reflector = $this->make(ReflectionClass::class, ['argument' => $constructorClassName]);
        $constructor = $reflector->getConstructor();
        $constructorParameters = $constructor->getParameters();

        $boundParameters = array();
        foreach ($constructorParameters as $parameterNumber => $parameter) {
            if (array_key_exists($parameterNumber, $parameters)) {
                // Numeric indexed arguments
                $boundParameters[] = $parameters[$parameterNumber];
            } else if (array_key_exists($parameterName = $parameter->getName(), $parameters)) {
                // Named arguments
                $boundParameters[] = $parameters[$parameterName];
            } else if (($parameterClass = $parameter->getClass())
                && $this->has($parameterType = $parameterClass->getName())) {
                // Injected arguments
                $boundParameters[] = $this->get($parameterType);
            } else {
                try {
                    // Default value.
                    $boundParameters[] = $parameter->getDefaultValue();
                } catch (ReflectionException $e) {
                    throw new DependencyException('Could not bind parameter #' . $parameterNumber
                        . ' ($' . $parameterName . ')');
                }
            }
        }

        // Let the invocation begin!
        $constructor->invokeArgs($object, $boundParameters);
    }

    /**
     * Registers an array of providers
     * @param array $providers
     * @throws Exception
     */
    public function registerProviders(array $providers)
    {
        $this->registeringProviders = true;
        $size = count($providers);
        $this->providerInstances = new SplFixedArray($size);
        $this->registeredProviderInstances = new SplStack();

        // Throw available providers in an array. There's no guarantee we load them in the order of dependency.
        foreach (array_values($providers) as $index => $provider) {
            $this->providerInstances[$index] = $this->container->get($provider);
        }

        try {
            $this->providerInstances->rewind();
            while ($this->providerInstances->valid()) {
                /** @var ProviderInterface $provider */
                $provider = $this->providerInstances->current();

                if ($provider !== null) {
                    // We need to mark this entry as null because we may iterate the array (at worst O(n^2)) during
                    // dependency resolution. We don't want to try to register it twice! If providers are listed in
                    // order, this will function at O(n).
                    $this->providerInstances[$this->providerInstances->key()] = null;

                    $provider->register();
                    $this->registeredProviderInstances->push($provider);
                }

                $this->providerInstances->next();
            }
        } catch (Exception $e) {
            $this->providerInstances = null;
            throw $e;
        } finally {
            $this->registeringProviders = false;
        }
    }

    /**
     * Boots all registered providers
     */
    public function bootRegisteredProviders()
    {
        while (!$this->registeredProviderInstances->isEmpty()) {
            /** @var ProviderInterface $provider */
            $provider = $this->registeredProviderInstances->pop();
            $provider->boot();
        }
        $this->providerInstances = null;
    }

    /**
     * Returns the current Container instance
     * @return Container
     * @deprecated This only exists for backwards compatibility with old global functions.
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Sets the current Container instance
     * @param Container $instance
     * @deprecated This only exists for backwards compatibility with old global functions.
     */
    public static function setInstance(Container $instance)
    {
        static::$instance = $instance;
    }

    /**
     * @see Container::has
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * @see Container::get
     * @param string $name
     * @return mixed
     */
    public function &offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * @see Container::set
     * @param string $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @see Container::set
     * @param string $name
     * @return mixed
     */
    public function offsetUnset($name)
    {
        $this->set($name, null);
    }
}
