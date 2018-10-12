# Module packages
We aim to keep code modular and self-contained (as possible).

### Installing modules
Installed modules are defined in the `<theme-package>/config/bindings.php`:

```php
Installer::INSTALLED_MODULES => [
    REW\Module\HelloWorld\ModuleController::class
]
```

### Module basics
At it's very core, a module is simply a class that implement `InstallableInterface`.

```php
<?php

namespace REW\Module\BoilerplateModule;

use REW\Core\Interfaces\InstallableInterface;

/**
 * Module controller class
 * @package REW\Module\BoilerplateModule
 */
class ModuleController implements InstallableInterface {

    /**
     * Perform any installation tasks
     * @return void
     */
    public function install () {
        // do stuff here
   }

}
```

Using dependency injection, the constructor is provided it's requirements:

```php
/**
 * @var YourDependentInterface
 */
protected $depends;

/**
 * @param YourDependentInterface @depends
 */
public function __construct (YourDependentInterface $depends) {
    $this->depends = $depends;
}
```

### Module patterns
Here are some common use cases and design patterns:

#### Defining website routes
via [`REW\Core\Interfaces\HooksInterface`](https://git.rewhosting.com/rew/rew-framework-interfaces/blob/master/src/HooksInterface.php)

#### Rendering HTML using Twig templates
[`REW\View\Interfaces\FactoryInterface`](https://git.rewhosting.com/rew-core/rew-view/blob/master/src/Interfaces/FactoryInterface.php) is used for rendering templates:

```php
use REW\View\Interfaces\FactoryInterface;

class ViewExample {

    const TEMPLATE_FILE = '/path/to/view.html.twig';

    protected $view;

    public function __construct (FactoryInterface $view) {
        $this->view = $view;
    }

    public function getTemplateHtml () {
        return $this->view->render(
            $this->getTemplateFile(),
            $this->getTemplateData()
        );
    }

    public function getTemplateFile () {
        return static::TEMPLATE_FILE;
    }

    public function getTemplateData () {
        return [];
    }

}
```

#### Including stylesheets and scripts
Assets can be included by using [`REW\Core\Interfaces\PageInterface`](https://git.rewhosting.com/rew/rew-framework-interfaces/blob/dev/src/PageInterface.php):

```php
use REW\Core\Interfaces\PageInterface;

class AssetExample {

    protected $page;

    public function __construct (PageInterface $page) {
        $this->page = $page;
    }

    public function addPageAssets () {
        $this->page->addStylesheet('/path/to/styles.css');
        $this->page->addJavascript('/path/to/script.js');
    }

}
```

#### Working with request data
Use [`Psr\Http\Message\ServerRequestInterface`](https://github.com/php-fig/http-message/blob/master/src/ServerRequestInterface.php)
as defined by the [PSR-7 HTTP message interfaces](http://www.php-fig.org/psr/psr-7/).

```php
$method  = $this->request->getMethod();
$body    = $this->request->getBodyParams();
$query   = $this->request->getQueryParams();
$cookies = $this->request->getCookieParams();
$files   = $this->request->getUploadedFiles();
$server  = $this->request->getServerParams();
```

***Do not use $_GET, $_POST or $_REQUEST.***

```php
use Psr\Http\Message\ServerRequestInterface;

class RequestExample {

    protected $request;

    public function __construct (ServerRequestInterface $request) {
        $this->request = $request;
    }

    public function doRequestStuff () {

        // Is a POST request
        if ($this->request->getMethod() === 'POST') {

            // POST request data
            $postData = $this->request->getParsedBody();

        }

        // GET data from query string
        $getData = $this->request->getQueryParams();
        // query example: (?abc=123&xyz=456)
        // result: ['abc' => '123', 'xyz' => '456']

    }

}
```

#### Performing database queries
[`REW\Core\Interfaces\DBInterface`](https://git.rewhosting.com/rew/rew-framework-interfaces/blob/dev/src/DBInterface.php) is used to execute queries on the application's database.

```php
use REW\Core\Interfaces\DBInterface;
use PDO;

class DatabaseExample {

    protected $db;

    public function __construct (DBInterface $db) {
        $this->db = $db;
    }

    public function doDatabaseStuff () {
        try {
            $queryString = "SELECT * FROM `tableName`;";
            return $this->db->fetch($queryString);
        } catch (PDOException $e) {
            throw $e;
        }
    }

}
```
 
#### Displaying REW CRM notifications
[`REW\Backend\Interfaces\NoticesCollectionInterface`](https://git.rewhosting.com/rew/rew-framework/blob/dev/httpdocs/backend/classes/Interfaces/NoticesCollectionInterface.php) is used to display user feedback within the REW CRM.

```php
use REW\Backend\Interfaces\NoticesCollectionInterface;
use Exception;

class FeedbackExample {

    protected $notices;

    public function __construct (NoticesCollectionInterface $notices) {
        $this->notices = $notices;
    }

    public function doFeedbackStuff () {
        try {

            // Show a success message
            $this->notices->success('Worked as expected.');

        // Unexpected errorr
        } catch (Exception $e) {
            $this->notices->error('Something went wrong.');

        }

    }

}
```
 
#### Database migrations and seeders
via [`REW\Core\Interfaces\HooksInterface`](https://git.rewhosting.com/rew/rew-framework-interfaces/blob/master/src/HooksInterface.php)

## Full example

Let's bring this all together for a full featured example:

```php
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use Psr\Http\Message\ServerRequestInterface;
use PDOException;
use Exception;

class FullFeaturedExample {

    protected $db;

    protected $log;

    protected $notices;

    protected $request;

    public function __construct (
        DBInterface $db
        LogInterface $log,
        NoticesCollectionInterface $notices,
        ServerRequestInterface $request
    ) {
        $this->db = $db;
        $this->log = $log;
        $this->notices = $notices;
        $this->request = $request;
    }

    public function __invoke () {
        try {

            // Get database record for request (?id=#)
            $requestData = $this->request->getQueryParams();
            $recordData = $this->getRecord($requestData['id']);

            // Handle POST submission
            if ($this->request->getMethod() === 'POST') {
                try {

                    $requestBody = $this->request->getParsedBody();

                    $this->updateRecord($recordData['id'], [
                        'title' => $requestBody['title'],
                        'about' => $requestBody['about'],
                    ]);

                // Database error
                } catch (PDOException $e) {
                    $this->notices->error(
                        'Could not insert record.'
                    );

                // Unexpected error
                } catch (Exception $e) {
                    $this->notices->error(
                        'Something went wrong.'
                    );

                }
            }

        } catch (Exception $e) {
            $this->notices->error(
                'Could not find record.'
            );

        }
    }

    public function getRecord ($recordId) {
        try {
            $queryString = "SELECT `id` FROM `tableName` WHERE `id` = ?;";
            $query = $this->db->prepare($queryString);
            $query->execute([$recordId]);
            return $query->fetch();
        } catch (PDOException $e) {
            $this->log->error($e);
            throw $e;
        }
    }

    public function updateRecord ($recordId, $recordData) {
        try {
            $queryString = "UPDATE INTO `tableName` SET `title` = :title, `about` = :about WHERE `id` = :id;";
            $queryParams = array_merge([$recordData], ['id' => $recordId]);
            $query = $this->db->prepare($queryString);
            return $query->execute($queryParams);
        } catch (PDOException $e) {
            $this->log->error($e);
            throw $e;
        }
    }

}
```
