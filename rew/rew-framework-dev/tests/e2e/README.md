## Running e2e Tests

#### Install Git
https://git-scm.com/downloads

#### Install Composer
https://getcomposer.org/download/

#### You will need chromedriver
https://sites.google.com/a/chromium.org/chromedriver/getting-started

*include the ChromeDriver location in your PATH environment variable*

#### Start Selenium Server
http://www.seleniumhq.org/download/

```
java -jar selenium-server-standalone-3.3.1.jar
```

#### Checkout Framework & Install Dependencies

```
git clone https://git.rewhosting.com/rew/rew-framework.git
cd rew-framework && composer install --ignore-platform-reqs
```

#### Create an `<env>.yml` file
Create `tests/_envs/<env>.yml` containing a valid URL and API Key to a working installation.

#### Run Smoke Tests

```
vendor/bin/codecept run e2e --env=<env>
```
