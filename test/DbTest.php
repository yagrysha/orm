<?php
namespace Yagrysha\ORM;
class DbTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp() {
    }
    static public function tearDownAfterClass()
    {
        Db::query("DROP TABLE test_Persons");
        Db::close();
    }

    public function testInit() {
        $params = Db::init(array(
            'host' => '127.0.0.1',
            'dbname'=>'test',
            'port' => '99'
        ));
        $this->assertTrue($params['host'] == '127.0.0.1');
        $this->assertArrayHasKey('port', $params);

        $params = Db::init();
        $this->assertArrayHasKey('host', $params);
    }
    public function testConnect() {
        try{
            $this->assertInstanceOf('mysqli', Db::connect(), 'connect error');
        }catch (Exception $e){
            $this->fail('fail '.$e);
        }
    }

    public function testDebug(){
        $this->assertTrue(Db::debug());
        $this->assertFalse(Db::debug(false));
    }

    public function testEsc(){
        $this->assertEquals('hel\"%`\\\'lo', Db::esc('hel"%`\'lo'));
    }
    public function testQuery()
	{
		$this->assertTrue(Db::query("DROP TABLE IF EXISTS test_Persons"));
		$res = Db::query(
			"CREATE TABLE test_Persons
			(
			id int,
			LastName varchar(50),
			FirstName varchar(50),
			Address varchar(255),
			City varchar(100), PRIMARY KEY (id)
			); "
		);
		$this->assertTrue($res);
    }
    public function testInsert(){
        $ins = Db::insert('test_Persons',
            [
                'LastName'=>'LastName1',
                'FirstName'=>'fname 1',
                'Address'=>'place1',
                'City'=>'Paris'
            ]
        );
        $this->assertEquals(0,$ins);
    }
    public function testSelect(){
        $res = Db::select('test_Persons');
        $this->assertEquals('LastName1', $res[0]['LastName']);
        $res = Db::select('test_Persons', [
            'where'=>['City'=>'mma']
        ]);
        $this->assertEquals([], $res);
        $res = Db::select('test_Persons', [
            'select'=>'City'
        ]);
        $this->assertEquals([['City'=>'Paris']], $res);
        $res = Db::rows('test_Persons', ['City'=>'Paris'], 'City');
        $this->assertEquals([['City'=>'Paris']], $res);

        $res = Db::selectOne('test_Persons', [
            'select'=>'City'
        ]);
        $this->assertEquals(['City'=>'Paris'], $res);
        $res = Db::row('test_Persons', ['City'=>'Paris'], 'City');
        $this->assertEquals(['City'=>'Paris'], $res);


        $res = Db::selectCell('test_Persons', [
            'select'=>'City'
        ]);
        $this->assertEquals('Paris', $res);
        $res = Db::cell('test_Persons', 'City');
        $this->assertEquals('Paris', $res);
    }

    public function testReplace(){
        $res = Db::replace('test_Persons', [ 'LastName'=>'LastName2']);
        $this->assertEquals(0,$res);
        $res = Db::cell('test_Persons', 'City');
        $this->assertEquals(null, $res);
    }

    public function testUpdate(){
        Db::update('test_Persons', ['City'=>'123'], ['id'=>0]);
        $this->assertEquals('123', Db::cell('test_Persons', 'City'));
    }

    public function testCount(){
        $this->assertEquals(1, Db::count('test_Persons'));
    }

    public function testDelete(){
        $this->assertTrue(Db::delete('test_Persons', ['id'=>0]));
        $this->assertEquals(0, Db::count('test_Persons'));
    }

    public function testTransaction(){
        Db::transaction();
        $this->assertEquals(0, Db::insert('test_Persons', ['LastName'=>'testtrans0']));
        $this->assertEquals(0, Db::insert('test_Persons', ['id'=>1,'LastName'=>'testtrans1']));
        $this->assertEquals(2, Db::count('test_Persons'));
        $this->assertTrue(Db::update('test_Persons', ['LastName'=>'up'], ['id'=>1]));
        $this->assertEquals('up', Db::cell('test_Persons', 'LastName', ['id'=>1]));
        Db::rollback();
        $this->assertEquals(0, Db::count('test_Persons'));
        $this->assertEquals(0, Db::insert('test_Persons', ['LastName'=>'testtrans0']));
        $this->assertEquals(0, Db::insert('test_Persons', ['id'=>1,'LastName'=>'testtrans1']));
        Db::commit();
        $this->assertEquals(2, Db::count('test_Persons'));
        $this->assertNotEquals('up', Db::cell('test_Persons', 'LastName', ['id'=>1]));
    }
}