Doctrine-Extensions
===================

[![Build Status](https://travis-ci.org/cgraefe/doctrine-extensions.svg?branch=master)](https://travis-ci.org/cgraefe/doctrine-extensions)

This is a collection of useful types and other minor extensions to the Doctrine DBAL and ORM.

UnixTimeType
------------
UnixTimeType is a custom Doctrine mapping type for time-stamp values represented in unix time, i.e. seconds since Jan 1, 1970.

```php
// Register custom type during boot-strap.
\Doctrine\DBAL\Types\Type::addType('unixtime', '\Graefe\Doctrine\Type\UnixTimeType');

...

/**
 * @ORM\Column(name="created", type="unixtime", nullable=false)
 */
private $created;
```
