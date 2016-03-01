<?php
/**
 * User: cgraefe
 * Date: 01.03.2016
 * Time: 09:20
 */

namespace Graefe\Doctrine\Type;


use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

// http://symfony.com/doc/current/cookbook/doctrine/dbal.html#registering-custom-mapping-types
// http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html

/**
 * Custom Doctrine mapping type for time-stamp values represented in unix time, i.e. seconds
 * since Jan 1, 1970.
 *
 * See also:
 * http://symfony.com/doc/current/cookbook/doctrine/dbal.html#registering-custom-mapping-types
 * http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html
 *
 * @package Graefe\Doctrine\Type
 */
class UnixTimeType extends Type
{
    const NAME = 'unixtime';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return ($value !== null)
            ? intval($value->format('U')) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }

        if ($value>=0 && $value<2147483648) {
            $val = \DateTime::createFromFormat('U', $value);
        } else {
            $val = \DateTime::createFromFormat('Y-m-d\TH:i:sP', date('c', $value));
        }
        //1901-12-13T21:45:52+01:00
        if ( ! $val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), 'integer');
        }

        return $val;
    }
}
