<?php
namespace Yagrysha\ORM;

use \PersonItem;

class MapperTraitTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		echo "\nstart " . __CLASS__;
		Db::init(
			array(
				'host' => '127.0.0.1',
				'dbname' => 'test',
			)
		);
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

	public function testFindByPk()
	{
		$this->assertNull(PersonItem::findByPk(1));
		$person = new PersonItem();
		$person->name = 1;
		$person->save();
		$this->assertEquals($person, PersonItem::findByPk(1));
		$this->assertEquals($person, PersonItem::findById(1));
	}

	public function testFindAll()
	{
		$person = PersonItem::findById(1);
		$persons = PersonItem::findAll([]);
		$this->assertInternalType('array', $persons);
		$this->assertEquals([$person->getIdString() => $person], Mapper::findAll($person));
	}

	public function testFind()
	{
		$person = PersonItem::find(['id' => 1]);
		$this->assertInstanceOf('PersonItem', $person);
		$this->assertEquals(1, $person->getId());
	}

	public function testFindByParams()
	{
		$person = PersonItem::findByParams(['where' => ['id' => 1]]);
		$this->assertInstanceOf('PersonItem', $person);
		$this->assertEquals(1, $person->getId());
	}

	public function testFindByParamsAll()
	{
		$person = PersonItem::findById(1);
		$persons = PersonItem::findByParamsAll(['where' => ['id' => 1]]);
		$this->assertInternalType('array', $persons);
		$this->assertEquals([$person->getIdString() => $person], Mapper::findAll($person));
	}

	public function testFindByField(){
		$person = PersonItem::findByField('id',1);
		$this->assertInstanceOf('PersonItem', $person);
		$this->assertEquals(1, $person->getId());
	}
	public function testFindByFieldAll(){
		$person = PersonItem::findById(1);
		$persons = PersonItem::findByFieldAll('id',1);
		$this->assertInternalType('array', $persons);
		$this->assertEquals([$person->getIdString() => $person], Mapper::findAll($person));
	}
	public function testGetById(){
		$person = PersonItem::getById(1);
		$this->assertInstanceOf('PersonItem', $person);
		$this->assertEquals(1, $person->getId());
		$this->assertTrue($person->isLoaded());
		$person = PersonItem::getById(2);
		$this->assertFalse($person->isLoaded());
	}
	public function testCreate(){
		$person = PersonItem::create(['name'=>'N1']);
		$this->assertInstanceOf('PersonItem', $person);
		$this->assertNull($person->getId());
		$this->assertEquals('N1', $person->name);
	}
	public function testFindOrCreate(){
		$person = PersonItem::findOrCreate(1);
		$this->assertTrue($person->isLoaded());
		$person = PersonItem::findOrCreate(2);
		$this->assertFalse($person->isLoaded());
		$this->assertEquals(2, $person->getId());
		$person->insert();
		$person = PersonItem::findOrCreate(2);
		$this->assertTrue($person->isLoaded());
	}
}