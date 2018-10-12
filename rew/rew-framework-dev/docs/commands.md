# Console commands
The application framework includes CLI scripts that are ran using the [`bin/console`](../bin/console) command: 

```bash
$ bin/console <command> [options] [<args>]
```

These commands are used during both the [installation](./installation.md) and [migration](./migration.md) process.

## Available commands

List all available console commands using:
 
```bash
$ bin/console list
```

The [`REW\Command`](../httpdocs/inc/classes/Command) and [`REW\Backend\Command`](../httpdocs/backend/classes/Command) namespace contains available console commands:

|  Command | Description |
| :------- | :---------- |
| `cache:reset` | Clear local filesystem cache and memcache server. |
| `changelog:create` | Generate `changelog/unrelease/*.yml` changelog file. |
| `changelog:release <release-tag>` | Merge unreleased changelogs (`-o` overwrites current changelog, `-c` commits the changelog). |
| `config:env <skin> [<scheme>]` | Generate `config/env/*.yml` files (`-f` to overwrite existing files) |
| `db:drop <branch>` [--force] | Drop all tables & functions from target database |
| `db:export <filename>` | Backup database to `.sql.gz` file |
| `db:import <filename>` | Import database from `sql[.gz]` file |
| `db:patch <commit>` [--skip <patch>] | Perform SQL/PHP patches since `<commit>` |
| `db:reset <branch>` [--seed] | Execute rollback/migrate/install/seeder to perform smoke-tests |
| `dialer:sync <domain>` | Synchronise REW Dialer accounts (run after `REW_PARTNERS_ESPRESSO` is changed) |
| `git:checkout <branch>` | Checkout latest framework branch. |
| `git:deploy` | Commits ignored files and pushes them from "dev" to the "live" site. **This command must only be ran on the *`master`* branch.** |
| `server:rsync <source> [<dest>]` | Copy source files to destination using `rsync` |
