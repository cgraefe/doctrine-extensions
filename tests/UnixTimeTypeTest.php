<?php

use Graefe\Doctrine\Type;

/**
 * User: cgraefe
 * Date: 01.03.2016
 * Time: 09:23
 */
class UnixTimeTypeTest extends PHPUnit_Framework_TestCase
{
    static private $dateCurrent;
    static private $dateFarFuture;
    static private $dateEndOfEpoch;
    static private $dateLongPast;

    public static function setUpBeforeClass()
    {
        \Doctrine\DBAL\Types\Type::addType('unixtime', '\Graefe\Doctrine\Type\UnixTimeType');

        $timezone = new DateTimeZone('UTC');

        self::$dateCurrent = DateTime::createFromFormat('Y-m-d H:i:s', '2016-03-01 13:37:00');
        self::$dateCurrent->setTimeZone($timezone);

        self::$dateFarFuture = DateTime::createFromFormat('Y-m-d H:i:s', '2048-03-01 13:37:00');
        self::$dateFarFuture->setTimeZone($timezone);

        self::$dateEndOfEpoch = DateTime::createFromFormat('Y-m-d H:i:s', '2038-01-19 04:14:07');
        self::$dateEndOfEpoch->setTimeZone($timezone);

        self::$dateLongPast = DateTime::createFromFormat('Y-m-d H:i:s', '1919-03-01 13:37:00');
        self::$dateLongPast->setTimeZone($timezone);
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDatabasePlatformMock()
    {
        $mock = $this->getMockForAbstractClass(
            'Doctrine\DBAL\Platforms\AbstractPlatform',
            array(
                'getName',
                'getIntegerTypeDeclarationSQL',
            )
        );
        $mock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mysql'));
        $mock->expects($this->any())
            ->method('getIntegerTypeDeclarationSQL')
            ->with($this->anything())
            ->will($this->returnValue('our integer declaration'));
        return $mock;
    }

    public function testConstruction()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('unixtime');
        $this->assertNotNull($type);
        $this->assertInstanceOf('\Graefe\Doctrine\Type\UnixTimeType', $type);
    }

    public function testGetSQLDeclarationMapsToGetIntegerTypeDeclarationSQL ()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('unixtime');
        $this->assertEquals('our integer declaration', $type->getSQLDeclaration([], $this->getDatabasePlatformMock()));
    }

    public function testConvertToDatabaseValue()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('unixtime');

        $this->assertNull($type->convertToDatabaseValue(null, $this->getDatabasePlatformMock()));

        $this->assertEquals(1456835820, $type->convertToDatabaseValue(self::$dateCurrent, $this->getDatabasePlatformMock()));
        $this->assertEquals(2147483647, $type->convertToDatabaseValue(self::$dateEndOfEpoch, $this->getDatabasePlatformMock()));
        $this->assertEquals(-1604316180, $type->convertToDatabaseValue(self::$dateLongPast, $this->getDatabasePlatformMock()));
    }

    public function testConvertToPHPValue()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('unixtime');
        $this->assertNull($type->convertToPHPValue(null, $this->getDatabasePlatformMock()));
        $this->assertEquals(self::$dateCurrent, $type->convertToPHPValue(1456835820, $this->getDatabasePlatformMock()));
        $this->assertEquals(self::$dateEndOfEpoch, $type->convertToPHPValue(2147483647, $this->getDatabasePlatformMock()));
        $this->assertEquals(self::$dateLongPast, $type->convertToPHPValue(-1604316180, $this->getDatabasePlatformMock()));
    }


}
