<?php
/**
 * @author ykmship@yandex-team.ru
 * Date: 11/10/14
 */

namespace Kozz\Tests;

use PHPUnit_Framework_TestCase;


class EMongoDBTest extends PHPUnit_Framework_TestCase
{

  /**
   * @var \EMongoDB
   */
  protected $mongo;

  protected $dbName;
  protected $collectionName;

  protected function setUp()
  {
    $this->dbName         = uniqid("testDB_");
    $this->collectionName = uniqid("testCollection_");

    $mongo = new \EMongoDB();
    $mongo->connectionString = 'mongodb://127.0.0.1';
    $mongo->dbName = $this->dbName;

    $this->mongo = $mongo;
  }

  protected function tearDown()
  {
    $mongo = clone $this->mongo;
    $mongo->dropDb();
  }

  public function testConnection()
  {
    $mongo = clone $this->mongo;

    $mongo->connect();
    $connection = $mongo->getConnection();
    $this->assertInstanceOf('MongoClient', $connection);

  }

  public function testMongoClient()
  {

    $mongo = clone $this->mongo;

    $mongo->connect();
    $connection = $mongo->getConnection();

    $db = $connection->selectDB($this->dbName);
    $this->assertInstanceOf('MongoDB', $db);

    $collection = $db->selectCollection($this->collectionName);
    $this->assertInstanceOf('MongoCollection', $collection);

    $collection->insert([]);

    $this->assertSame([$this->collectionName], $db->getCollectionNames());

  }

  public function testEMongoDBConnection()
  {

    $mongo = clone $this->mongo;

    $mongo->connect();

    $db = $mongo->getDbInstance();
    $this->assertInstanceOf('MongoDB', $db);

    $collection = $db->selectCollection($this->collectionName);
    $this->assertInstanceOf('MongoCollection', $collection);

    $collection->insert([]);

    $this->assertSame([$this->collectionName], $db->getCollectionNames());


  }

  public function testSetDbInstance()
  {
    $mongo = clone $this->mongo;
    $mongo->connect();

    $nameDb   = uniqid('testDB_1_');
    $nameColl = uniqid('testColl1_');

    $mongo->getDbInstance()->createCollection($nameColl);

    $mongo->setDbInstance($nameDb);

    $mongo->getDbInstance()->createCollection($nameColl);

    $dbsList = $mongo->getDatabaseNames();

    $this->assertTrue(in_array($this->dbName, $dbsList));
    $this->assertTrue(in_array($nameDb, $dbsList));

    $drop = $mongo->dropDb();

    $this->assertTrue(1 == $drop['ok']);
    $this->assertEquals($nameDb, $drop['dropped']);
  }

  public function testDrop()
  {
    $mongo = clone $this->mongo;
    $mongo->connect();
    
    $mongo->getDbInstance()->createCollection($this->collectionName);
    $this->assertTrue(in_array($this->dbName, $mongo->getDatabaseNames()));

    $drop = $mongo->dropDb();

    $this->assertTrue(1 == $drop['ok']);
    $this->assertEquals($this->dbName, $drop['dropped']);

    $this->assertFalse(in_array($this->dbName, $mongo->getDatabaseNames()));

  }


} 