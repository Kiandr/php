# Changelog
This document provides developers information on how to integrate the changelog commands into there workflow.

## What does the Changelog Tool do?
There 2 commands added to console for handling changelog creation. First the create command:

```bash
$ console changelog:create
```

This will create a yml file in `~/app/changelogs/unrelease/` directory.

The release command:

```bash
$ console changelog:release <release-tag>
```

This will merge unreleased changelogs and has the optional flags (`-o`) overwrites current changelog, and (`-c`) commits the changelog.

## Adding a Change
Changelogs has been categorized into the following type of changes: 
- added
- changed
- deprecated
- removed
- fixed
- security

Your merge request can contain multiple changelogs, and the description should be to the point of the category.

Always create your changelogs on the branch you are working on, you can not create changelogs on master and dev branches.

You should create these changelogs after you have submitted a merge request and commit it to the branch.

## Releasing a Changelog

Once a tag is planned, the changelog should be generated. The release only builds what is in the unrelease directory, this allows it to be more flexible than a diff of last tag to current tag.
The release tag ideally should be following semantic versioning standards, and would be created on the release log commit.

The changelog will also be prepended to the `~/app/changelogs/ARCHIVE.md`, this allows storage and ability to remove old logs from the main changelog markdown file.

