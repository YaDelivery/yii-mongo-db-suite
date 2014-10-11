<?php
/**
 * @author ykmship@yandex-team.ru
 * Date: 11/10/14
 */

namespace Kozz\Tests;

use Kozz\Tests\Model\MongoModel;
use Kozz\Tests\Model\MongoModelUser;
use Kozz\Tests\Model\MongoModelUserAddress;
use PHPUnit_Framework_TestCase;


class EMongoEmbeddedDocumentTest extends PHPUnit_Framework_TestCase
{

  /**
   * @var \EMongoDB
   */
  protected $mongo;

  protected static $id;

  protected function setUp()
  {
    $dbName         = uniqid("testDB_");

    $mongo = new \EMongoDB();
    $mongo->connectionString = 'mongodb://127.0.0.1';
    $mongo->dbName = $dbName;

    $r = new \ReflectionClass('\EMongoDocument');
    $m = $r->getMethod('setMongoDBComponent');
    $m->setAccessible(true);
    $m->invoke(null, $mongo);
    $this->mongo = $mongo;
  }

  protected function tearDown()
  {
    $this->mongo->dropDb();
  }

  public function testInit()
  {
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModelUser', MongoModelUser::model());
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModelUser', new MongoModelUser());

    $this->assertInstanceOf('Kozz\Tests\Model\MongoModelUserAddress', new MongoModelUserAddress());

  }

  public function testSaveAndFind()
  {

    $model = new MongoModelUser();
    $model->name = 'test';

    $address = new MongoModelUserAddress();
    $address->city = 'LA';
    $model->addresses[] = $address;

    $success = $model->save();

    $this->assertTrue($success);

    $model  = MongoModelUser::model()->find();
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModelUser', $model);

    $address = $model->addresses[0];
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModelUserAddress', $address);

    $this->assertInstanceOf('Kozz\Tests\Model\MongoModelUser', $address->getOwner());
  }



} 