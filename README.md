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


MySqlEnumType
-------------
MySqlEnumType is an abstract base class for mapping ENUM types in MySQL.

```php
// Define type.
class ShopModeType extends \Graefe\Doctrine\Type\MySqlEnumType
{
    protected function getValues() { return array('b2b','b2c'); }
    public function getName() { return 'shopmode'; }
}

...

// Register type during boot-strap.
\Doctrine\DBAL\Types\Type::addType('shopmode', 'ShopModeType');

...

/**
 * @ORM\Column(name="mode", type="shopmode", nullable=false)
 */
private $mode;
```

RAND()
-----
The Rand class provides the RAND() function to DQL for selecting random rows. (Caveat: Improper use might
cause serious performance problems.)

```php
// Register function.
$em->getConfiguration()->addCustomNumericFunction('RAND', '\\Graefe\\Doctrine\\Functions\\Rand');

...

$qb->select('d')
    ->addSelect('RAND() AS randval')
    ->from('Dummy', 'd')
    ->orderBy('randval')
    ->setMaxResults(1);
```
