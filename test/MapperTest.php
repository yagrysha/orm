<?php
namespace Yagrysha\ORM;
use \PersonItem;
class MapperTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		echo "\nstart ".__CLASS__;
        Db::init(array(
             'host' => '127.0.0.1',
             'dbname'=>'test',
        ));
		Db::query("DROP TABLE IF EXISTS test_Persons");
		Db::query(
			"CREATE TABLE test_Persons
			(
			id int NOT NULL AUTO_INCREMENT,
			name varchar(50),
			PRIMARY KEY (id)
			); "
		);
	}

    static public function tearDownAfterClass()
    {
		Db::query("DROP TABLE test_Persons");
        Db::close();
    }

    public function testFindAll(){
        $person = new PersonItem();
        $this->assertNull(Mapper::findAll($person));
        $this->assertNull(Mapper::findAll('PersonItem'));
        $person->name = 1;
        $person->save();
        $this->assertEquals([$person->getIdString()=>$person], Mapper::findAll($person));
    }

    public function testFindBySql(){
        $person = Mapper::findBySql('PersonItem', ['id'=>1]);
        $this->assertInstanceOf('PersonItem', $person);
        $this->assertEquals(1,$person->getId());
    }

    public function testFindBySqlAll(){
        $person = Mapper::findBySql('PersonItem', ['id'=>1]);
        $persons = Mapper::findBySqlAll('PersonItem', ['id'=>1]);
        $this->assertInternalType('array', $persons);
        $this->assertEquals([$person->getIdString()=>$person], $persons);
    }

    public function testFindByParamsAll(){
        $person = Mapper::findBySql('PersonItem', ['id'=>1]);
        $persons = Mapper::findByParamsAll('PersonItem', ['where'=>['id'=>1]]);
        $this->assertInternalType('array', $persons);
        $this->assertEquals([$person->getIdString()=>$person], $persons);
    }

    public function testFindByParams(){
        $person = Mapper::findByParams('PersonItem', ['where'=>['id'=>1]]);
        $this->assertInstanceOf('PersonItem', $person);
    }

    public function testFindByPk(){
        $person = Mapper::findByPk('PersonItem', 1);
        $this->assertInstanceOf('PersonItem', $person);
        $this->assertEquals(1, $person->getId());
    }

    public function testFindOrCreate(){
        $person = Mapper::findOrCreate('PersonItem', 1);
        $this->assertTrue($person->isLoaded());
        $person = Mapper::findOrCreate('PersonItem', 2);
        $this->assertFalse($person->isLoaded());
        $this->assertEquals(2, $person->getId());
        $person->insert();
        $person = Mapper::findOrCreate('PersonItem', 2);
        $this->assertTrue($person->isLoaded());
    }

    public function testBuildMultiple(){
        $persons = Mapper::buildMultiple('PersonItem', [
            ['id'=>1, 'name'=>'Name'],
            ['id'=>2, 'name'=>'Name2']
        ]);
        $this->assertInternalType('array', $persons);
        $this->assertCount(2, $persons);
        $this->assertInstanceOf('PersonItem', $persons[1]);
        $this->assertEquals(2, $persons[2]->getId());
    }

    public function testBuild(){
        $person = Mapper::build('PersonItem', ['id'=>1, 'name'=>'Name']);
        $this->assertInstanceOf('PersonItem', $person);
        $this->assertEquals(1, $person->getId());
        $this->assertEquals('Name', $person->name);
    }
}


 