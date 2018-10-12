# Deployment guide
This guide talks about the recipe used by domains created in [rewHUB](http://gce.hub.rewhosting.com/nodes/) and the steps to push changes from the ***dev*** site to the ***live*** site. 

## "***dev***" vs "***live***"
Every domain created in rewHUB (using any of the 4.x codebase recipes) hosts a Git repository containing the websites files:

```
ssh://<user>@<host>:/var/www/vhosts/dev.<domain>/app/.git
```
 
A remote repository named `live` exists, this represents the deployed filesystem:

```bash
$ git remote -v
live    ssh://<user>@<host>:/var/www/git/<domain>.git (fetch)
live    ssh://<user>@<host>:/var/www/git/<domain>.git (push)
```
 
## Pushing to live site

Some paths are included in the `.gitignore` file, preventing them from showing up as modified files.
You will need to add them in order to push them to the live website.

```bash
$ git add -f config/env/
$ git add -f boot/loaders/
$ git add -f node_modules/
$ git add -f httpdocs/backend/build/
$ git add -f httpdocs/backend/node_modules/
$ git add -f vendor/rew/rew-framework-interfaces/
$ git add -f vendor/rew/rew-page-objects/
$ git add -f vendor/
```

*TODO: 4.8 codebase recipe should build these using a pre-receive hook?*

## REW Stage
[REW Stage](https://github.com/Real-Estate-Webmasters/rew-stage) is a tool that allows REW developers to browse & manage a Git repository from the browser.

## REW Enterprise Deployment
https://git.rewhosting.com/enterprise/deploy