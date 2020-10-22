Gravity PDF Developer Toolkit
==========================

[![Build Status](https://travis-ci.org/GravityPDF/gravity-pdf-developer-toolkit.svg?branch=development)](https://travis-ci.org/GravityPDF/gravity-pdf-developer-toolkit)  [![codecov](https://codecov.io/gh/GravityPDF/gravity-pdf-developer-toolkit/branch/development/graph/badge.svg)](https://codecov.io/gh/GravityPDF/gravity-pdf-developer-toolkit)

Gravity PDF Developer Toolkit is a commercial plugin [available from GravityPDF.com](https://gravitypdf.com/shop/developer-toolkit/). The plugin is hosted here on a public GitHub repository in order to better facilitate community contributions from developers and users. If you have a suggestion, a bug report, or a patch for an issue, feel free to submit it here.

If you are using the plugin on a live site, please purchase a valid license from the website. **We cannot provide support to anyone that does not hold a valid license key**.

# About

This Git repository is for developers who want to contribute to Gravity PDF Developer Toolkit. **Don't use it in production**. For production use, [purchase a license and install the packaged version from our online store](https://gravitypdf.com/shop/developer-toolkit/).

The `development` branch is considered our bleeding edge branch, with all new changes pushed to it. The `master` branch is our latest stable version of Gravity PDF Developer Toolkit.

# Installation

Before beginning, ensure you have [Git](https://git-scm.com/), [Composer](https://getcomposer.org/) and [Yarn](https://yarnpkg.com/en/docs/install) installed and their commands are globally accessible via the command line.

1. Clone the repository using `git clone https://github.com/GravityPDF/gravity-pdf-developer-toolkit/`
1. Open your terminal / command prompt to the Gravity PDF Developer Toolkit root directory and run `composer install`
1. Copy the plugin to your WordPress plugin directory (if not there already) and active through your WordPress admin area

## Building and Testing

Gravity PDF Developer Toolkit can be built for production use using the bash script `./bin/build.sh <version> [branch]`.

### Generating API Documentation

We use [Sami](https://github.com/FriendsOfPHP/Sami/) to automatically generate our developer documentation. To generate the documentation run `./bin/build-docs.sh`. You'll find the contents inside `dev-documentation`.

### Run Unit Tests

#### PHPUnit

We use PHPUnit to test out all the PHP we write. The tests are located in `tests/phpunit/unit-tests/`

To create the testing environment you'll need to use a unix-style CLI, and have the following commands available: unzip, tar, curl/wget, svn, mysqladmin

1. Run `bash ./bin/install-wp-tests.sh gravitypdf_test root root localhost` where `root root` is substituted for your mysql username and password, and `localhost` is your database address.
2. Upon success, you can run `vendor/bin/phpunit` to fire off the testing suite