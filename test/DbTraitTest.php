<?php
namespace Yagrysha\ORM;

use \PersonItem;

class DbTraitTest extends \PHPUnit_Framework_TestCase
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

	public function testDb()
	{
		$person = ['name'=>'Name'];
		$this->assertEquals(0, PersonItem::count());
		$id = PersonItem::ins($person);
		$this->assertEquals(1, PersonItem::count());

		$this->assertEquals(1, $id);
		$this->assertEquals($person['name'], PersonItem::cell('name', ['id'=>$id]));
		$this->assertEquals($person['name'], PersonItem::selectCell(['select'=>'name', 'where'=>['id'=>$id]]));

		$this->assertEquals(1, PersonItem::ins(['id'=>1, 'name'=>'name2'], true));

		$this->assertEquals(['id'=>1, 'name'=>'name2'], PersonItem::row(['id'=>1]));
		$this->assertEquals(['id'=>1, 'name'=>'name2'], PersonItem::selectOne(['where'=>['id'=>1]]));

		$this->assertTrue(PersonItem::upd(['name'=>'name3'], ['id'=>1]));

		$this->assertEquals(['id'=>1, 'name'=>'name3'], PersonItem::rowId(1));

		$this->assertTrue(PersonItem::updId(1, ['name'=>'name1']));

		$this->assertEquals([['id'=>1, 'name'=>'name1']], PersonItem::rows(['id'=>1]));
		$this->assertEquals([['id'=>1, 'name'=>'name1']], PersonItem::select(['where'=>['id'=>1]]));

		$this->assertTrue(PersonItem::del(['id'=>1]));
		$this->assertTrue(PersonItem::delId(1));

		$this->assertInstanceOf('mysqli_result', PersonItem::query('SELECT * FROM '.PersonItem::TABLE));
		$this->assertInstanceOf('mysqli_result', PersonItem::query(['select'=>'*']));
	}
}