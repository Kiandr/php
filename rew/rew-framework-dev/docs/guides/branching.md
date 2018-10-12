# QA Environment
You must first setup your own QA environment using the [installation guide](../installation.md).

## QA Testing
QA testing is performed on Git branches. In the commands below, `<branch>` indicates the Jira issue ID.
As an example, `JIRA-1234` would be used in place of `<branch>` for this issue: https://realestatewebmasters.atlassian.net/browse/JIRA-1234

### Switching between branches

1. Change working path to application's directory
2. Checkout a local branch & pull the latest changes
3. Configure [theme package](../installation.md#themes) in `~/app/config/env/general.yml`
4. Install theme package, it's dependencies, and build it's assets
4. Clear the local filesystem cache and flush the memcache server
5. Download all of the framework dependencies (via composer/npm)
6. Rebuild application's static assets (REW CRM)
7. Rollback and recreate database with seed data


This is all performed & handled using the command:

```bash
$ console git:checkout <branch>
```
