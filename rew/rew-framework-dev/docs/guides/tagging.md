# Version Tagging

All package tags follow Semantic Versioning 2.0.0, to learn more [visit their site](http://semver.org/).

## NPM Packages

When submitting a merge request for a NPM Package, the code reviewers job is:

- review MR and confirm any breaking changes
- merge the MR
- decide the version for the package, is it a large breaking change or a small bugfix
- edit the `package.json` file, and change the version to the new tag version
- finally create a tag with the name of the version you set in `package.json`

You should be able to now update the rew-framework `package.json` file to the new tag and `npm install` it.

## Composer Packages

For Composer packages, the code reviewers job is:

- review MR and confirm any breaking changes
- merge the MR
- decide the version for the package, is it a large breaking change or a small bugfix
- tag the repo with new version, then wait for [packagist](https://packagist.rewhosting.com/packages/rew-core/rew-view) to fetch (5-15min)

Then you can run `composer update rew-core/rew-view` to get the new version.
