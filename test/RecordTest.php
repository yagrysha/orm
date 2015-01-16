<?php
namespace Yagrysha\ORM;
class RecordTest extends \PHPUnit_Framework_TestCase
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
            id int,
            LastName varchar(50),
            FirstName varchar(50),
            Address varchar(255),
            City varchar(100), PRIMARY KEY (id)
            ); "
        );
    }

    static public function tearDownAfterClass()
    {
        Db::query("DROP TABLE test_Persons");
        Db::close();
    }

    public function testItem(){
        $person = new Person();
        $this->assertFalse($person->isLoaded());

		$person->setId(1);
		$this->assertEquals(1,$person->getId());
		$this->assertEquals([Person::PRK=>1],$person->getPkArray());
		$this->assertEquals(1,$person->getIdString());

		$person->FirstName = 'Name1';
		$this->assertEquals('Name1',$person->FirstName);
		$this->assertEquals($person->get('FirstName'),$person->FirstName);

		$person->set('City', 'London');
		$this->assertEquals('London',$person->City);

		$person->setArray([
				'LastName'=>'lname',
				'Address'=>'1 street'
			]);
		$this->assertEquals('lname',$person->LastName);

		unset($person->LastName, $person->Address, $person->City);
		$this->assertNull($person->LastName);
		$this->assertEmpty($person->LastName);

		$this->assertEquals(['id'=>1, 'FirstName'=>'Name1'], $person->getFields());
		$this->assertEquals(['id'=>1, 'FirstName'=>'Name1'], $person->getChangedFields());

		$person2 = clone $person;
		$this->assertEquals(['id'=>null, 'FirstName'=>'Name1'], $person2->getFields());
		$person2->reset();
		$this->assertEquals([], $person2->getFields());

		$person->reset();
		$personData = [
			Person::PRK=>2,
			'FirstName'=>'Name2'
		];
		$person->init($personData);
		$this->assertTrue($person->isLoaded());
		$this->assertEquals($personData, $person->getFields());
		$this->assertEquals([], $person->getChangedFields());
		$this->assertFalse($person->isChanged());
		$this->assertFalse($person->isChangedPk());
		$this->assertFalse($person->isChanged('FirstName'));

		$person->FirstName='Name3';
		$this->assertTrue($person->isChanged());
		$this->assertTrue($person->isChanged('FirstName'));
    }

	public function testSave(){
		$personData = [
			Person::PRK=>1,
			'FirstName'=>'Name1',
			'LastName'=>'2',
			'Address'=>'2',
			'City'=>'4'
		];
		$person = new Person();
		$person->init($personData);
		$person->save();
		$fromDb = Db::row(Person::TABLE, $person->getPkArray());
		$this->assertEquals($fromDb, $personData);

		$this->assertFalse($person->insert());
		$this->assertEquals(1062, Db::connect()->errno, 'Duplicate entry for key PRIMARY');

		$person->delete();
		$fromDb = Db::row(Person::TABLE, $person->getPkArray());
		$this->assertEmpty($fromDb);

		$this->assertTrue($person->insert(), Db::connect()->errno);
		$person->City='London';
		$person->update();
		$this->assertEquals('London', Db::cell(Person::TABLE, 'City', $person->getPkArray()));

		$person->delete();
	}
}

/**
 * Class Person
 * @package Yagrysha\ORM
 * @property $id
 * @property $LastName
 * @property $FirstName
 * @property $Address
 * @property $City
 */
class Person extends Record {
    const TABLE = 'test_Persons';
}
 