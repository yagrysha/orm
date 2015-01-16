<?php
namespace Yagrysha\ORM;
class ItemTest extends \PHPUnit_Framework_TestCase
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
			Name varchar(50),
			PRIMARY KEY (id)
			); "
		);
	}

    static public function tearDownAfterClass()
    {
		Db::query("DROP TABLE test_Persons");
        Db::close();
    }

    public function testItem(){
        $person = new PersonItem();
        $person->Name='11';
        $this->assertEquals(1,$person->save());
        $this->assertFalse($person->save());

        $this->assertEquals(1, $person->getId());
        $person->setId(null);
        $person->Name='22';
        $this->assertEquals(2,$person->save());
        $this->assertEquals(2, $person->getId());

        $this->assertEquals(2, Db::count(PersonItem::TABLE));

        $newp = new PersonItem();
        $newp->setId(1);
        $newp->delete();

        $this->assertEmpty(Db::row(PersonItem::TABLE, $newp->getPkArray()));
        $this->assertEquals(Db::row(PersonItem::TABLE, $person->getPkArray()), $person->getFields());
        $this->assertEquals(1, Db::count(PersonItem::TABLE));
    }
}