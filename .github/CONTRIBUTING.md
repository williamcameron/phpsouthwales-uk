# Contributing to the PHP South Wales website

- Pull requests are welcome, and we are happy to mentor new contributors to this project or to PHP or Drupal projects in general.

- Please [create an issue](https://github.com/PHPSouthWales/phpsouthwales-uk/issues/new) before submitting a pull request, just to make sure that the change is one that will be accepted and to agree on any implementation details.

- Pull requests should be created from issue branches, and should use `master` as the target branch.

- The issue branch should be descriptive, and include the GitHub issue number. For example, `add-contributing-file-GH-196`. This makes it easy to see which issue the pull request relates to.

- Please use descriptive commit messages, and use the commit body to describe why that change was being made and any potential issues or side effects. We like atomic commits, and all commit history is kept (we don't squash commits when merging pull requests).

- Also include the issue number within the issue body, and feel free to use the `Fixes` or `Closes` keywords so that the issue is automatically closed with the pull request is merged.

  For example:
```
Add CONTRIBUTING.md

Add a CONTRIBUTING.md file to make things easier and clearer for new
contributors.

Fixes #196
```

- Please ensure that your issue branch is up to date with the `master` branch, and is fast-forwardable. Rebase your issue branch and force push if needed). We always use fast forward merges to keep a clean and linear Git history.

- Changes should include updated documentation and tests, where applicable.

- Automated checks are run via GitHub Actions on each pull request to ensure that the automated tests are passing, the code standards are met, etc. All checks need to be passing for a pull request to be merged.
