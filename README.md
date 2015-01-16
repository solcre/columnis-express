[![Build Status](https://api.travis-ci.org/solcre/columnis-express.svg?branch=master)](https://travis-ci.org/solcre/columnis-express)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/solcre/columnis-express/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/solcre/columnis-express/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/solcre/columnis-express/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/solcre/columnis-express/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/solcre/columnis-express/version.svg)](https://packagist.org/packages/solcre/columnis-express)
[![Latest Unstable Version](https://poser.pugx.org/solcre/columnis-express/v/unstable.svg)](//packagist.org/packages/solcre/columnis-express)
[![Total Downloads](https://poser.pugx.org/solcre/columnis-express/downloads.svg)](https://packagist.org/packages/solcre/columnis-express)
[![License](https://poser.pugx.org/solcre/columnis-express/license.svg)](https://packagist.org/packages/solcre/columnis-express)

Columnis Express
=======================

Introduction
------------
Columnis Express is the new version of Columnis CMS. It is a simple ZF2 application that consumes Columnis API, assigns the data to the selected template engine and renders the request page.

Installation
------------

Using Composer (recommended)
----------------------------
The recommended way to get a working copy of this project is to clone the repository
and use `composer` to install dependencies using the `create-project` command:

    curl -s https://getcomposer.org/installer | php --
    php composer.phar create-project solcre/columnis-express path/to/install

Alternately, clone the repository and manually invoke `composer` using the shipped
`composer.phar`:

    cd my/project/dir
    git clone git://github.com/solcre/columnis-express.git
    cd columnis-express
    php composer.phar self-update
    php composer.phar install

(The `self-update` directive is to ensure you have an up-to-date `composer.phar`
available.)

Another alternative for downloading the project is to grab it via `curl`, and
then pass it to `tar`:

    cd my/project/dir
    curl -#L https://github.com/solcre/columnis-express/tarball/master | tar xz --strip-components=1

You would then invoke `composer` to install dependencies per the previous
example.


Using Git submodules
--------------------
Alternatively, you can install using native git submodules:

    git clone git://github.com/solcre/columnis-express.git --recursive

Requirements
----------------

### Server

PHP 5.4+
cURL

### Libraries Used

Zend Framework 2
Guzzle -> Used to consume the Columnis API
AssetsManager + Assetic + filters -> Used to manage and minify stylesheets and scripts
Smarty -> Used as template engine.

Configuration
----------------