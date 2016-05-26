<?php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Tools\Setup;


/**
 * @ORM\Entity
 */
class Dummy
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

}

/**
 * User: cgraefe
 * Date: 26.05.2016
 * Time: 13:38
 */
class RandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public function getEntityManager()
    {
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__), true, null, null, false);
        $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);
        $em = EntityManager::create($connectionOptions, $config);
        return $em;
    }

    public function testQueryBuilderGeneratesSQL()
    {
        $em = $this->getEntityManager();
        $em->getConfiguration()->addCustomNumericFunction('RAND', '\\Graefe\\Doctrine\\Functions\\Rand');

        $qb = $em->createQueryBuilder();
        $qb->select('d')->addSelect('RAND() AS randval')->from('Dummy', 'd')->orderBy('randval')->setMaxResults(1);

        $this->assertRegExp('/^SELECT .* RAND\\(\\) .* FROM Dummy .* ASC LIMIT 1$/', $qb->getQuery()->getSQL());
    }

}
