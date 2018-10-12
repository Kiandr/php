# REW Hooks
The framework uses hooks to execute code as needed, where needed.

## Hook basics
Hooks are just [callables](http://php.net/types.callable).

 - Some modify data
 - Some return data
 - Most are just events
 
 
### Available hooks
See [`HooksInterface`][HooksInterface]
 
### Using hooks
 
##### Defining a hook
Bind hook function with the expected parameters:

```php
$hooks->on($hookName, function ($hookParams) {
    // do stuff here
}, 10);
```

When binding multiple hooks, `$priority` can be set to specify the desired order.

See `HooksInterface::on($name, $callable, $priority)`

##### Running a hook
Run the hook by name, providing the required parameters:

```php
$hooks->hook($hookName)->run($hookParams);
```

## Usage example

 - Use DI to provide `HooksInterface` dependency
 - Bind available hooks to public class methods
 
```php
<?php

use REW\Core\Interfaces\HooksInterface;

class ClassName {

    /**
     * Hooks to install
     * @var string[]
     */
    const INSTALL_HOOKS = [
        HooksInterface::HOOK_NAME => 'hookMethod'
    ];

    /**
     * @var HooksInterface
     */
    protected $hooks;
        
    /**
    * @param HooksInterface $hooks
    */
    public function __construct (HooksInterface $hooks) {
        $this->hooks = $hooks;
    }
    
    /**
     * Install hooks
     * @return void
     */
    public function installHooks () {
        foreach (self::INSTALL_HOOKS as $hookName => $methodName) {
            $this->hooks->on($hookName, [$this, $methodName]);
        }
    }
    
    /**
     * Example of hook 
     */
    public function hookMethod () {
        // do hook stuff
    }
    
}
```

[HooksInterface]: https://git.rewhosting.com/rew/rew-framework-interfaces/blob/4.8.0/src/HooksInterface.php
