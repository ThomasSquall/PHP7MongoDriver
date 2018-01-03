## MongoDriver for PHP7

With the advent of PHP7 the old mongodb driver is no more supported.

The new driver available is a little bit low-level compared to the previous one so it can be a bit complicated to work with.

This is what this library was conceived for.

### Prerequisites

Before using this library you should make sure to have installed PHP7.0 or major and MongoDb driver from pecl.

For those using a Linux Debian distribution just run:

```
$ sudo pecl install mongodb
```

### Usage

At first you need to define a connection string.

The format for connection strings is:

```
mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]][/[database][?options]]
```

For more information see the link: https://docs.mongodb.com/manual/reference/connection-string/

Once defined you need to instantiate a new Adapter:

```
use MongoDriver\Adapter;

// Enstablish a connection.
$adapter = new Adapter();
$adapter->connect(CONNECTION_STRING);
```

At this point you want to select a Database where do your query:

```
$adapter->selectDB('myDatabase');
```
NOTE: you could select a database directly on the constructor passing the database name as the 2nd parameter.

Once selected the database we can simply query for the collection we want:

```
$items = $adapter->find('myCollection');
```

You can also filter your query:
```
use MongDriver\Filter;

$filters =
[
    new Filter('myField1', 'myValue1', Filter::IS_EQUALS),
    new Filter('myField2', ['myValue2', 'myValue3'], Filters::IS_IN_ARRAY)
];

$items = $adapter->find('myCollection', $filters);
```

Hope you guys find this library useful.

Please share it and give me a feedback :)

Thomas