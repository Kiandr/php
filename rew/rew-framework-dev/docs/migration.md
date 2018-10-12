# Migrating from 4.x to 4.8
This guide will provide you with a number of steps to help you understand how to upgrade your application.

## Prerequisites

- A node & domain created in [rewHUB](http://gce.hub.rewhosting.com/nodes/):
  - The node must reside on **GCE**.
  - The **4.8 Codebase** recipe must be used.
  
- SSH connection with [agent forwarding enabled](http://xerxes/wiki/How_To_Set_Up_Agent_Forwarding).
  
## Checkout code

Let's first merge in the latest code:

```bash
$ cd ~/app
$ git remote add s https://git.rewhosting.com/rew/rew-framework.git
$ git fetch --tags framework uat
$ git merge <current release tag>
```

## Install dependencies

Now that we have the code, we need to get the required dependencies:

```bash
$ composer install
$ npm install
```

## Configure environment

Before we can continue, we need to create our `~/app/config/env/*.yml` files: 

```bash
$ bin/console config:env <skin> [<scheme>] [<locale>] -idx-feed=<feed> -d <database> -u <username> -p <password>
```

## Import existing database
Make sure you have agent forwarding set up for the following commands.

We need a database to work with. Let's copy over the existing database (**using REW cloud credentials**):

```bash
$ bin/console db:export --full --hostname=<host> -d <database> -u <rew-username> -p <rew-password>
$ bin/console db:import <filename>
```

Now it's time to patch the database to include the latest changes:
(the commit is the last commit of the framework on live branch)
```bash
$ bin/console db:patch <commit>
$ php ~/app/tools/phinx.php migrate
```

## Copy existing uploads
Use the `server:rsync` command to copy over the existing site's uploads folder: 

```bash
$ bin/console server:rsync <user>@<host>:app/httpdocs/uploads/ ~/app/httpdocs/uploads/
```

## Merge existing changes

```bash
$ rm ~/app/vendor -rf
$ git remote add old ssh://<user>@<host>:/var/www/vhosts/dev.<domain>/app/.git
$ git pull --no-commit --no-ff old master
$ git reset -- ~/app/vendor
$ composer install
```

## Fix conflicts
***This might take a while.***

* For any backend merge conflicts you can reset and checkout the backend directory, as no custom code is allowed on the backend files.
* If you need some help, see [GitHub's guide to resolving a merge conflict using the command line](https://help.github.com/articles/resolving-a-merge-conflict-using-the-command-line/).
* If your up-stream is gone, you will need to `git checkout -- HEAD <file>` to revert them

*All resolved?* Time to continue and commit all the changes:

```bash
$ git merge --continue
```

### Commonly known conflicts 

#### Module configuration
 
The [httpdocs/inc/classes/Settings.php](../httpdocs/inc/classes/Settings.php) file will have conflicts. Move module settings to the `modules.yml` file:
 
```bash
$ vi ~/app/config/env/modules.yml
```

See [config/defaults/modules.yml](../config/defaults/modules.yml) for more information.

#### Static calls need to be updated to use the new interface
Such as IDX_Compliance and Util_IDX.

Old: `IDX_Compliance::{whatever_method()}`

New: `\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->{whatever_method()};`

Old: `Util_IDX::{whatever_method()}`

New: `\Container::getInstance()->get(\REW\Core\Interfaces\Util\IDXInterface::class)->{whatever_method()};`

## Dev to Live
Once ready to push everything live, follow this [guide](deployment.md).

# Troubleshooting
 - Did you [enable agent forwarding](http://xerxes/wiki/How_To_Set_Up_Agent_Forwarding)?
 
# Notable Changes

### `config/env/*.yml`
https://git.rewhosting.com/rew/rew-framework/tree/dev/config/env

### `tools/console.php <command>`
https://git.rewhosting.com/rew/rew-framework/blob/dev/tools/console.php

### Framework Interfaces
https://git.rewhosting.com/rew/rew-framework-interfaces

### Dependency Injection using `Container`
https://git.rewhosting.com/rew/rew-framework/blob/dev/config/bindings.php
