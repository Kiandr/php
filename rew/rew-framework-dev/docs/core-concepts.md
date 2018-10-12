[//]: # (Todo: Update examples section at bottom with master link)
# Core Concepts

## Overview
In `rew-framework` (and other projects), we are trying to introduce a more
abstracted development approach to make development cleaner and more
predictable.

To do this, we are using defining our set of core concepts within the REW ecosystem. This includes **controllers**, **models**, **factories**, **datastores** and **interfaces**.

## Basic Definitions
**Controllers** are classes that perform (ideally) all of the business logic of the required action.

**Models** are classes that house properties for objects, but have no real actions themselves. Models are just "Plain PHP Objects" that defines the schema of data.

**Datastores** are classes that perform actions against storage media. This can be a flat file on a filesystem, a database, or anything of that nature.

**Factories** are classes used to create models from available. 

**Plugins** are packages that are used to change functionality (often with hooks).

**Modules** are packages that provide a UI for some functionality. 
 - An example would be a `Communities` module, which provides a twig template and some server-side code to expose the data to the template.

**Interfaces** are used to define the contract between these objects.

## Core Rules

### Controllers
 - MUST be within `REW\Controller` namespace
 - MUST be suffixed with `Controller` in class name
 - MUST extend `REW\Controller\AbstractController`
 - MUST only contain 1 public method: `public function __invoke ()`
 - MUST only get dependencies through `public function __construct`
 - Properties MUST only contain injected dependencies through constructor
 
### Models
 - MUST be within `REW\Model` namespace
 - MUST not contain any injected dependencies
 - SHOULD be suffixed with `Model` in class name
 - MUST be immutable (no regular "setter" methods)
 - MUST only contain the following types of methods:
   - Getter method, named as `get<PropName>`
   - With method, named as `with<PropName>`
   - `StdObject::__construct`
   - `StdObject::__toString`
   - `JsonSerializable::jsonSerialize`

### Datastores 
 - MUST be within `REW\Datastore` namespace
 - MUST be suffixed with `Datastore` in class name
 - MUST get dependencies through `public function __construct`
 - MUST contain only methods that accept and return `Model` objects
 - MUST contain only "CRUD" type methods with prefixes:
   - `get`
   - `create`
   - `update`
   - `delete`
 
### Factories 
 - MUST be within `REW\Factory` namespace
 - MUST be suffixed with `Factory` in class name
 - MUST be named by the model type: `<Model>Factory`
 - MUST only get dependencies through `public function __construct`
 - Properties MUST only contain injected dependencies through constructor
 - MUST not contain any static methods
 - MUST return `Model` objects based on factory type
 - MUST only contain methods in naming format:
   - `createFrom<Type>` (eg: `createFromArray`)

### Plugins
 - MUST be within `REW\Plugin` namespace if a core plugin
 - MUST be within `REW\Theme\<THEME>\Plugin` namespace if a theme plugin
 - MUST be suffixed with `Plugin` in class name

### Modules
- MUST be within `REW\Module` namespace if a core plugin
- MUST be within `REW\Theme\<THEME>\Module` namespace if a theme module
- MUST be suffixed with `Module` in class name


### Controller Example
In `HypotheticalUserController.php`
```
/**
 * @param UserValidatorInterface $userValidator
 * @param UserModelFactoryInterface $userModelFactory
 * @param UserDatastoreInterface $userDatastore
 */
public function __construct
(
  UserValidatorInterface $userValidator,
  UserModelFactoryInterface $userModelFactory,
  UserDatastoreInterface $userDatastore
) {
  $this->userValidator = $userValidator;
  $this->userModelFactory = $userModelFactory;
  $this->userDatastore = $userDatastore;
}

/**
 * @param array $params
 * @return UserResultModel
 * @throws APIError if $params fails validation or on Datastore error.
 */
public function createUser($params)
{
  // Validate the parameters and make sure we have what we need to create a user.
  $this->userValidator->validateAgainstParams($params);

  // Create a model to send off to the datastore.
  $userModel = $this->userModelFactory->createFromData($params);

  // Create the user in the database.
  $userId = $this-userDatastore->createUser($userModel);

  // Return now-created user to the caller.
  return $this->getUser($userId);
}
```


### Examples in `rew-framework`
 - [Anything in the `/src` directory](https://git.rewhosting.com/rew/rew-framework/tree/barb-dev/src)

### Examples of Plugins and Modules
 - [Example Module](https://gitlab.com/Real-Estate-Webmasters/rew-theme/discover/tree/master/src/Module/)
 - [Example Plugin](https://gitlab.com/Real-Estate-Webmasters/rew-theme/discover/tree/master/src/Plugin/)
