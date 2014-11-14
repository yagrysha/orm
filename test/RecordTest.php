<?php
namespace Yagrysha\ORM;
class RecordTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Db::init(array(
             'host' => '127.0.0.1',
             'dbname'=>'test',
        ));
    }


    static public function tearDownAfterClass()
    {
        Db::close();
    }

    public function testInit() {

    }
    public function testConnect() {
        try{
            $this->assertInstanceOf('mysqli', Db::connect(), 'connect error');
        }catch (Exception $e){
            $this->fail('fail '.$e);
        }
    }

    public function testItem(){
        $person = Person::findByPk(0);
        var_dump($person);
    }
}
class Person extends Item {
    use DbTrait, MapperTrait;
    const TABLE = 'test_Persons';
}
 