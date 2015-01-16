<?php
namespace Yagrysha\ORM;
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

    public function testFindAll(){
        $person = new PersonItem();
        $person->Name = '11';
        $persons = Mapper::findAll('PersonItem');
        p($persons);
    }
}


 