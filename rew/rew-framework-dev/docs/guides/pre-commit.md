# [pre-commit](http://pre-commit.com/) 
> Git hook scripts are useful for identifying simple issues before submission to code review. We run our hooks on every commit to automatically point out issues in code such as missing semicolons, trailing whitespace, and debug statements. By pointing these issues out before code review, this allows a code reviewer to focus on the architecture of a change while not wasting time with trivial style nitpicks.

## Installation
pre-commit is installed using the cURL method:

```bash
$ curl http://pre-commit.com/install-local.py | python
```

## Hook Definitions
The `.pre-commit-config.yaml` file contains the configuration for the pre-commit hooks used:

* [`check-merge-conflict`](https://github.com/pre-commit/pre-commit-hooks)
* [`no-commit-to-branch`](https://github.com/pre-commit/pre-commit-hooks)
* [`check-json`](https://github.com/pre-commit/pre-commit-hooks)
* [`check-yaml`](https://github.com/pre-commit/pre-commit-hooks)
* [`check-xml`](https://github.com/pre-commit/pre-commit-hooks)
* [`eslint`](https://github.com/pre-commit/mirrors-eslint) 
* [`stylelint`](https://github.com/awebdeveloper/pre-commit-stylelint)
* [`php-lint`](https://github.com/digitalpulp/pre-commit-php#php-lint)
* [`php-cs`](https://github.com/digitalpulp/pre-commit-php#php-cs)

## Hook Installation
Once the hooks are defined, the hooks are downloaded and installed to the Git repository using the command:

```bash
$ pre-commit install
```
  
## Tips & tricks
Running pre-commit hooks against all files:

```bash
$ pre-commit run --all-files
```