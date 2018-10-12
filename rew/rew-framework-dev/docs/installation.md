# Installation instructions
This guide walks through the steps to get an REW website up and running.

## Prerequisites

- A node & domain created in [rewHUB](http://gce.hub.rewhosting.com/nodes/):
  - The node must reside on **GCE**.
  - The **4.8 Codebase** recipe must be used.
  
- SSH connection with [agent forwarding enabled](http://xerxes/wiki/How_To_Set_Up_Agent_Forwarding).

If you're installing a REW developer site, use the existing [`web-46-development-01-zocc`](http://gce.hub.rewhosting.com/nodes/web-46-development-01-zocc/domains/) node. 

## Checkout latest code

Log into your remote domain via SSH and checkout the latest code:

```bash
$ cd ~/app
$ git remote add framework https://git.rewhosting.com/rew/rew-framework.git
$ git fetch framework dev
$ git checkout -b dev framework/dev
```

## <a name="themes"></a> Available Themes

| Theme | `<theme>` | `<theme-package>` | `<theme-version>` |
|:------|:----------|---------|---------|
| [Discover Theme](https://gitlab.com/Real-Estate-Webmasters/rew-theme/discover)   | `REW\Theme\Discover\Theme` | `rew-theme/discover`  |  `dev-release`
| [Vision Design](../httpdocs/inc/skins/ce/README.md) | `REW\Theme\Enterprise\Theme` | `rew-theme/enterprise` | `*`
| [The Fredrik](../httpdocs/inc/skins/fese)           | `fese`     |
| [The Barbara](../httpdocs/inc/skins/bcse)           | `bcse`     |
| [The Elite](../httpdocs/inc/skins/elite)            | `elite`    |
| [LEC 2015](../httpdocs/inc/skins/lec-2015)          | `lec-2015` |
| [LEC 2013](../httpdocs/inc/skins/lec-2013)          | `lec-2013` |

## Configure environment

Before we begin, the application's settings must be created accordingly.

**You must use *2 spaces* for indentation.**

1. Configure MySQL credentials: `vi config/env/databases.yml`
    ```yml
    databases:
      default:
        username: 
        password: 
        database: 
    ```

2. Configure the site settings: `vi config/env/general.yml`
    ```yml
    skin: <theme> 
    skin_scheme: 
    idx_feed: 
    ```

3. (Optional) Configure modules (example): `vi config/env/modules.yml`
    ```yml
    modules:
      REW_BLOG_INSTALLED: true
    ```

Note that all options specified in `env/*.yml` will override the options specified in `defaults/*.yml` which will in
turn override the options specified in the Settings class.

## Install dependencies

Install PHP dependencies using [Composer](https://getcomposer.org) and JavaScript dependencies using [npm](https://www.npmjs.com/):

```bash
$ composer install
$ npm install
```

## Install theme package (optional)

Now we need to install the theme's package and it's dependencies.

```bash
$ composer require <theme-package>:<theme-version>
$ cd ~/app/vendor/<theme-package>
$ npm install
```

## Build theme assets (optional)

The *Discover* theme requires it's assets (CSS & JS) to be built.

**Development Mode:**

```bash
$ npm run build
```

**Production Mode:**

```bash
$ npm run ship
```

## Build CRM assets

The [REW CRM](../httpdocs/backend/README.md) requires it's CSS and JS assets to be build before it will work. This can be done using the shortcut:

```bash
$ cd ~/app/httpdocs/backend
$ npm install
$ npm run build-css
```

**Development Mode:**

```bash
$ npm run build-js
```

**Production Mode:**

```bash
$ npm run ship-js
```

## Install MySQL database
Now we're ready to setup the application's database and install the site's default content: 

```bash
$ console db:reset
```

_**Pro-tip:** provide the `--seed` option to  run the [DemoSeeder](database/seeds/DemoSeeder.php) - adding sample content to test with._

## Deployment
Refer to [Deployment Guide](./deployment.md)

## Troubleshooting
 - If using the GIT deployment strategy (dev/live), use `git add -f config/env/*.yml` in order to add and commit the
version-ignored config files.

- **Q:** Fatal error being shown: `LESS compiler not found`<br> 
  **A:** `cd ~/app && npm install`

- **Q:** What are the logins for the REW CRM?<br>
  **A:** The username is `admin` and password is `h3eA3qP3`.
