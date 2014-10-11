<?php
/**
 * @author ykmship@yandex-team.ru
 * Date: 11/10/14
 */

namespace Kozz\Tests\Model;


class MongoModel extends \EMongoDocument
{

  public $name;
  public $value;
  /**
   * @param string $className
   *
   * @return MongoModel
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function getCollectionName()
  {
    return 'test_mongo_collection';
  }

  public function rules()
  {
    return [
      ['name, value', 'safe']
    ];
  }


}
