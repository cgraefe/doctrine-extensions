<?php



/**
 * User: cgraefe
 * Date: 03.03.2016
 * Time: 16:59
 */
class MySqlEnumTypeTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        \Doctrine\DBAL\Types\Type::addType('shopmode', 'ShopModeType');
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
            )
        );
        $mock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mysql'));
        return $mock;
    }

    public function testConstruction()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('shopmode');
        $this->assertNotNull($type);
        $this->assertInstanceOf('ShopModeType', $type);
    }

    public function testGetSQLDeclaration ()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('shopmode');
        $declaration = $type->getSQLDeclaration([], $this->getDatabasePlatformMock());
        $this->assertStringStartsWith('ENUM', $declaration);
    }

    public function testConvertToDatabaseValue()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('shopmode');

        $this->assertEquals('b2b', $type->convertToDatabaseValue('b2b', $this->getDatabasePlatformMock()));
        $this->assertEquals('b2c', $type->convertToDatabaseValue('b2c', $this->getDatabasePlatformMock()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConvertToDatabaseValueRejectsValuesNotInEnum()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('shopmode');

        $type->convertToDatabaseValue('xxx', $this->getDatabasePlatformMock());
    }

    public function testConvertToPHPValue()
    {
        $type = \Doctrine\DBAL\Types\Type::getType('shopmode');
        $this->assertNull($type->convertToPHPValue(null, $this->getDatabasePlatformMock()));
        $this->assertEquals('b2b', $type->convertToPHPValue('b2b', $this->getDatabasePlatformMock()));
        $this->assertEquals('b2c', $type->convertToPHPValue('b2c', $this->getDatabasePlatformMock()));
    }

}


class ShopModeType extends \Graefe\Doctrine\Type\MySqlEnumType
{
    protected function getValues()
    {
        return array('b2b','b2c');
    }

    public function getName()
    {
        return 'shopmode';
    }
}