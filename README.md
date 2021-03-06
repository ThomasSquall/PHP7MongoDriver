# MongoDriver for PHP7

[![Latest Stable Version](https://poser.pugx.org/thomas-squall/php7-mongo-driver/v/stable.svg)](https://packagist.org/packages/thomas-squall/php7-mongo-driver) 
[![Build Status](https://travis-ci.org/ThomasSquall/PHP7MongoDriver.svg?branch=master)](https://travis-ci.org/ThomasSquall/PHP7MongoDriver)
[![Coverage Status](https://coveralls.io/repos/github/ThomasSquall/PHP7MongoDriver/badge.svg?branch=master)](https://coveralls.io/github/ThomasSquall/PHP7MongoDriver?branch=master)
[![codecov](https://codecov.io/gh/ThomasSquall/PHP7MongoDriver/branch/master/graph/badge.svg)](https://codecov.io/gh/ThomasSquall/PHP7MongoDriver)
[![Total Downloads](https://poser.pugx.org/thomas-squall/php7-mongo-driver/downloads.svg)](https://packagist.org/thomas-squall/php7-mongo-driver) 
[![License](https://poser.pugx.org/thomas-squall/php7-mongo-driver/license.svg)](https://packagist.org/packages/thomas-squall/php7-mongo-driver)

With the advent of PHP7 the old mongodb driver is no more supported.

The new driver available is a little bit low-level compared to the previous one so it can be a bit complicated to work with.

This is what this library was conceived for.

**!!! FOR DETAILED GUIDELINES CONSULT THE WIKI AT:**
https://github.com/ThomasSquall/PHP7MongoDriver/wiki

## Installation

Using composer is quite simple, just run the following command:
``` sh
$ composer require thomas-squall/php7-mongo-driver
```

## Prerequisites

Before using this library you should make sure to have installed PHP7.0 or major and MongoDb driver from pecl.

For those using a Linux distribution (make sure to have pecl installed) just run:

``` sh
$ sudo pecl install mongodb
```

After that you should put the following string
``` sh
extension=mongodb.so
```
Inside your php.ini

## Usage

At first you need to define a connection string.

The format for connection strings is:

```
mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]][/[database][?options]]
```

For more information see the link: https://docs.mongodb.com/manual/reference/connection-string/

Once defined you need to instantiate a new Adapter:

``` php
use MongoDriver\Adapter;

// Enstablish a connection.
$adapter = new Adapter();
$adapter->connect(CONNECTION_STRING);
```

At this point you want to select a Database where do your query:

``` php
$adapter->selectDB('myDatabase');
```
NOTE: you could select a database directly on the constructor passing the database name as the 2nd parameter.

### Find

Once selected the database we can simply query for the collection we want:

``` php
$items = $adapter->find('myCollection');
```

You can also filter your query:
``` php
use MongDriver\Filter;

$filters =
[
    new Filter('myField1', 'myValue1', Filter::IS_EQUALS),
    new Filter('myField2', ['myValue2', 'myValue3'], Filters::IS_IN_ARRAY)
];

$items = $adapter->find('myCollection', $filters);
```

### Insert

If you want to insert an item you have simply to pass an array or an object to the insert function specifying the collection:

``` php
$item = new Person();
$item->name = 'Thomas';
$item->surname = 'Cocchiara');

// or: $item = ['name' => 'Thomas', 'surname' => 'Cocchiara'];

$adapter->insert('people', $item);
```

Hope you guys find this library useful.

Please share it and give me a feedback :)

Thomas