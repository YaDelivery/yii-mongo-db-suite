<?php
/**
 * @author Yury Kozyrev <ykmship@yandex-team.ru>
 */

namespace Kozz\Tests;

use PHPUnit_Framework_TestCase;

class LoadTest extends PHPUnit_Framework_TestCase
{
  public function testLoad()
  {
    $this->assertTrue(class_exists('EMongoCriteria'));
    $this->assertTrue(class_exists('EMongoCursor'));
    $this->assertTrue(class_exists('EMongoDB'));
    $this->assertTrue(class_exists('EMongoDocument'));
    $this->assertTrue(class_exists('EMongoDocumentBehavior'));
    $this->assertTrue(class_exists('EMongoEmbeddedDocument'));
    $this->assertTrue(class_exists('EMongoException'));
    $this->assertTrue(class_exists('EMongoGridFS'));
    $this->assertTrue(class_exists('EMongoModifier'));
    $this->assertTrue(class_exists('EMongoSoftDocument'));
    $this->assertTrue(class_exists('EMongoCacheDependency'));
    $this->assertTrue(class_exists('EMongoHttpSession'));
    $this->assertTrue(class_exists('EMongoLogRoute'));
    $this->assertTrue(class_exists('EMongoSort'));

    $this->assertTrue(class_exists('EEmbeddedArraysBehavior'));
    $this->assertTrue(class_exists('EMongoPartialDocument'));
    $this->assertTrue(class_exists('EMongoUniqueValidator'));

    $this->assertTrue(class_exists('CMongoUniqueValidator'));
    $this->assertTrue(class_exists('EMongoExistsValidator'));

  }

  public function testConnection()
  {
    $mongo = new \EMongoDB();
    $mongo->connectionString = 'mongodb://127.0.0.1';
    $mongo->dbName = 'test';

    $mongo->connect();
    $connection = $mongo->getConnection();
    $this->assertInstanceOf('MongoClient', $connection);

    $this->assertInstanceOf('MongoDB', $connection->selectDB('test'));
    $this->assertInstanceOf('MongoDB', $mongo->getDbInstance());
    $this->assertInstanceOf('MongoCollection', $mongo->getDbInstance()->selectCollection('testCollection'));
  }

}
