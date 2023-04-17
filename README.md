[![Build Status](https://api.travis-ci.org/solcre/columnis-express.svg?branch=master)](https://travis-ci.org/solcre/columnis-express)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/solcre/columnis-express/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/solcre/columnis-express/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/solcre/columnis-express/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/solcre/columnis-express/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/54f4ac894f31083e1b000b2a/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54f4ac894f31083e1b000b2a)
[![Latest Stable Version](https://poser.pugx.org/solcre/columnis-express/version.svg)](https://packagist.org/packages/solcre/columnis-express)
[![Latest Unstable Version](https://poser.pugx.org/solcre/columnis-express/v/unstable.svg)](//packagist.org/packages/solcre/columnis-express)
[![Total Downloads](https://poser.pugx.org/solcre/columnis-express/downloads.svg)](https://packagist.org/packages/solcre/columnis-express)
[![License](https://poser.pugx.org/solcre/columnis-express/license.svg)](https://packagist.org/packages/solcre/columnis-express)

# Columnis Express

## Introduction

Columnis Express is the new version of Columnis CMS. It is a simple ZF2 application that consumes Columnis API, assigns the data to the selected template engine and renders the request page.

## Installation

-  To run columnis-express on your computer you must have doker installed and running.

-  Clone repository and run docker

```bash
$ git clone https://github.com/solcre/columnis-express.git
$ cd columnis-express
$ docker-compose up -d
```

![run container done](https://s3.amazonaws.com/cdn.express-beta.solcre.com/images/Screen+Shot+2023-04-14+at+10.59.08.png)

If the console gives you the following, we are on the right track **don't worry about the warning**

-  Rename local.php.dist to local.php

```bash
$ cd config/autoload
$ mv local.php.dist local.php
```

-  Inside local.php change the client-number
   ![local php](https://s3.amazonaws.com/cdn.express-beta.solcre.com/images/Screen+Shot+2023-04-14+at+11.14.25.png)

-  Inside the docker terminal

```bash
php composer.phar install
```

-  Open localhost:8080 in your browser and if you get something like this (img), everything is ok
   ![404 columnis](https://s3.amazonaws.com/cdn.express-beta.solcre.com/images/Screen+Shot+2023-04-14+at+11.27.44.png)

-  Inside public_html paste all your project dir (templates, assets, css, etc) like this:
   ![404 columnis](https://s3.amazonaws.com/cdn.express-beta.solcre.com/images/Screen+Shot+2023-04-14+at+11.30.55.png)

   **Note:** for all this to work the .htacces, 404.html, index.php and php.ini files must be inside public_html and must never be modified, deleted or anything like that.

-  Right now you can go back to localhost:8080 and you should see your web site 🥳🍾

### Server

PHP 5.4+
cURL

### Libraries Used

Zend Framework 2

Guzzle 5 -> Used to consume the Columnis API

AssetsManager + Assetic + filters -> Used to manage and minify stylesheets and scripts

Smarty -> Used as template engine.

Apigility -> Used to retrieve templates and invalidate cache.
