<?php
namespace Yagrysha\ORM;
class RowTest extends \PHPUnit_Framework_TestCase
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
			iid int,
			Name varchar(50),
			PRIMARY KEY (id, iid)
			); "
		);
	}

    static public function tearDownAfterClass()
    {
		Db::query("DROP TABLE test_Persons");
        Db::close();
    }

    public function testItem(){
        $person = new PersonRow();
        $this->assertEquals([], $person->getId());
        $id = ['id'=>1, 'iid'=>1];
        $person->setId($id);
        $this->assertEquals($id, $person->getId());
        $this->assertEquals($id, $person->getPkArray());
        $this->assertEquals('1-1', $person->getIdString());
        $this->assertTrue($person->isChangedPk());
    }

	public function testSave(){
        $data = [
            'id'=>1, 'iid'=>1, 'Name'=>'2'
        ];
        $person = new PersonRow();
        $person->setArray($data);

        $this->assertEquals('1-1', $person->getIdString());
        $person->save();

        $fromDb = Db::row(PersonRow::TABLE, $person->getPkArray());
        $this->assertEquals($fromDb, $data);
        $person->id=2;
        $this->assertEquals('2-1', $person->getIdString());
        $person->save();

        $this->assertEquals(2, Db::count(PersonRow::TABLE));
	}
}

/**
 * Class Person
 * @package Yagrysha\ORM
 * @property $id
 * @property $iid
 * @property $Name
 */
class PersonRow extends Row {
    protected $_prk = ['id', 'iid'];
    const TABLE = 'test_Persons';
}
