<?php
/**
 * @author ykmship@yandex-team.ru
 * Date: 11/10/14
 */

namespace Kozz\Tests;

use Kozz\Tests\Model\MongoModel;
use PHPUnit_Framework_TestCase;


class EMongoDocumentTest extends PHPUnit_Framework_TestCase
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
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModel', MongoModel::model());
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModel', new MongoModel());

    $this->assertEquals(
      $this->mongo->getDbInstance()->selectCollection(MongoModel::model()->getCollectionName())->getName(),
      MongoModel::model()->getCollectionName()
    );
  }

  public function testSave()
  {
    $model = new MongoModel();
    $model->name  = 'testFieldName';
    $model->value = 'testFieldValue';

    $this->assertTrue($model->getIsNewRecord());
    $this->assertEmpty($model->getPrimaryKey());

    $status = $model->save();

    $this->assertTrue($status);
    $this->assertFalse($model->getIsNewRecord());
    $this->assertNotEmpty($model->getPrimaryKey());

    self::$id = $model->getPrimaryKey();
  }

  public function testFind()
  {
    $model = MongoModel::model()->find();

    $this->assertInstanceOf('Kozz\Tests\Model\MongoModel', $model);

    $models = MongoModel::model()->findAll();
    $this->assertTrue(is_array($models));
    $this->assertNotEmpty($models);
    $this->assertInstanceOf('Kozz\Tests\Model\MongoModel', reset($models));

  }

  public function testFindByPk()
  {
    $model = MongoModel::model()->findByPk(self::$id);

    $this->assertInstanceOf('Kozz\Tests\Model\MongoModel', $model);
  }

  public function testDelete()
  {
    $model = MongoModel::model()->find();

    $this->assertInstanceOf('Kozz\Tests\Model\MongoModel', $model);

    $success = $model->delete();
    $this->assertTrue($success);

    $models = MongoModel::model()->findAll();
    $this->assertTrue(is_array($models));
    $this->assertEquals(0, count($models));

  }

} 