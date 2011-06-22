<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test;

use Orient\Test\PHPUnit\TestCase;
use Orient\ODM\Manager;
use Orient\ODM\Mapper;
use Orient\ODM\Mapper\Annotations\Reader as AnnotationReader;


class ManagerTest extends TestCase
{
    public function setup()
    {
        $annotationReader = new AnnotationReader;
        $annotationReader->setAutoloadAnnotations(true);
        $this->mapper = new Mapper($annotationReader);
        $this->mapper->setDocumentDirectories(array('./Test/ODM/Document/Stub' => 'Orient\\'));
        
        $this->jsonRecord = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "is_true":   1,
            "is_false":  0,
            "@class":   "Address",
            "date":     "2011-01-01",
            "datetime":     "2011-01-01 21:00:00",
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0",
            "sample":   "ok"
         }');

        $this->jsonRecordWrongClass = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "@class":   "MNOMNOMONMONM",
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }');

        $this->jsonRecordNoClass = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }');
         
         $this->jsonCollection = array(
            json_decode('{
                "@type":    "d", 
                "@rid":     "#12:0", 
                "@version":  0, 
                "@class":   "Address",
                "street":   "Piazza Navona, 1",
                "type":     "Residence",
                "city":     "#13:0"
              }'),
            json_decode(  '{
                  "@type":    "d", 
                  "@rid":     "#13:0", 
                  "@version":  0, 
                  "@class":   "OCity",
                  "name":     "roma"
                }'),
            json_decode('{
                  "@type":    "d", 
                  "@rid":     "#12:0", 
                  "@version":  0, 
                  "@class":   "ForeignAddress",
                  "street":   "Piazza Navona, 1",
                  "type":     "Residence",
                  "city":     "#13:0"
              }'),
         );
         
    }
    
    public function testYouCanDecideWheterInjectACustomAnnotationReaderOrNotToTheMapper()
    {
        $annotationReader = new AnnotationReader;
        $annotationReader->setAutoloadAnnotations(true);
        $this->mapper = new Mapper($annotationReader);
        
        $this->assertInstanceOf('Orient\ODM\Mapper\Annotations\Reader', $this->mapper->getAnnotationReader());
        
        $this->mapper = new Mapper();
        
        $this->assertInstanceOf('Doctrine\Common\Annotations\AnnotationReader', $this->mapper->getAnnotationReader());
    }
    
    public function testAJsonGetsConvertedToAnObject()
    {   
        $object = $this->mapper->hydrate($this->jsonRecord);
        
        $this->assertInstanceOf('Orient\Test\ODM\Document\Stub\Contact\Address', $object);
    }

    /**
     * @expectedException Orient\Exception\Document\NotFound
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithoutAClass()
    {
        $object = $this->mapper->hydrate($this->jsonRecordNoClass);
    }

    /**
     * @expectedException Orient\Exception\Document\NotFound
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithAWrongClass()
    {
        $object = $this->mapper->hydrate($this->jsonRecordWrongClass);
    }

    public function testStringPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals('Residence', $object->getType());
    }
    
    public function testPropertiesCanHaveDifferentNamesInOrientAndPopo()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals('ok', $object->getExampleProperty());
    }
    
    public function testBooleanPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(true, $object->getIsTrue());
        $this->assertEquals(false, $object->getIsFalse());
    }

    public function testDatePropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $object->getDate());
        $this->assertEquals('2011-01-01', $object->getDate()->format('Y-d-m'));
    }

    public function testDatetimePropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $object->getDateTime());
        $this->assertEquals('2011-01-01 21:00:00', $object->getDateTime()->format('Y-d-m H:i:s'));
    }

    public function testAnAnnotatedPropertyNotPassedWithTheJSONIsNullByDefault()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(NULL, $object->getAnnotatedButNotInJson());
    }

    public function testPropertiesGetsMappedInTheObjectOnlyIfAnnotated()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(NULL, $object->getStreet());
    }

    public function testGettingTheDirectoriesInWhichTheMapperLooksForPOPOs()
    {
        $annotationReader = new AnnotationReader;
        $annotationReader->setAutoloadAnnotations(true);
        $this->mapper = new Mapper($annotationReader);
        $dirs = array(
            'dir'   => 'namespace',
            'dir2'  => 'namespace2',
        );
        $object = $this->mapper->setDocumentDirectories($dirs);

        $this->assertEquals($dirs, $this->mapper->getDocumentDirectories());
    }
    
    public function testNoRecordsIsLostWhenHydratingACollection()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertEquals(3, count($collection)); 
    }
    
    public function testHidratedCollectionsContainPopo()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('Orient\Test\ODM\Document\Stub\Contact\Address', $collection[0] );
        $this->assertInstanceOf('Orient\Test\ODM\Document\Stub\City', $collection[1] );
    }
    
    public function testOrientObjectsOfDifferentClassesCanBeMappedByASinglePopo()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('Orient\Test\ODM\Document\Stub\Contact\Address', $collection[2] );
    }
}
