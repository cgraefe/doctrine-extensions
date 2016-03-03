<?php
/**
 * User: cgraefe
 * Date: 03.03.2016
 * Time: 16:52
 */

namespace Graefe\Doctrine\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abstract base class for mapping ENUM types in MySQL based on the Doctrine cook-book example.
 *
 * @package Type
 */
abstract class MySqlEnumType extends Type
{

    /**
     * Set of allowed ENUM values.
     *
     * @return String[]
     */
    protected abstract function getValues();

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function ($val) {
            return "'" . $val . "'";
        }, $this->getValues());

        return "ENUM(" . implode(", ", $values) . ") COMMENT '(DC2Type:" . $this->getName() . ")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException("Invalid '" . $this->getName() . "' value.");
        }
        return $value;
    }

}