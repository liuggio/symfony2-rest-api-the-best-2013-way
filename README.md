REST API Symfony2: the best 2013 way
====================================

This is the code to support the article at [Symfony2 API REST the best way](http://welcometothebundle.com/rest-api-symfony2-the-best-2013-way/)

### Install with Composer

As Symfony uses [Composer][1] to manage its dependencies, the recommended way
to create a new project is to use it.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

Then, use the `create-project` command to generate a new Symfony application:

    php composer.phar create-project liuggio/rest-api-the-best-way-2013 blog-rest-symfony2
    cd blog-rest-symfony2

Composer will install Symfony and all its dependencies under the
`blog-rest-symfony2` directory.

### Run the test.

This repo is a demo/tutorial,
start with the first article running:

`git checkout -f part1`
`phpunit -c app`


[1]:  http://getcomposer.org/