<?php
/**
 * @author ykmship@yandex-team.ru
 * Date: 11/10/14
 */

namespace Kozz\Tests\Model;


class MongoModelUser extends \EMongoDocument
{

  public $name;
  public $addresses = [];
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
      ['name', 'safe']
    ];
  }


  public function behaviors()
  {
    return [
      [
        'class'             => 'EEmbeddedArraysBehavior',
        'arrayPropertyName' => 'addresses', // name of property
        'arrayDocClassName' => '\Kozz\Tests\Model\MongoModelUserAddress' // class name of documents in array
      ],
    ];
  }

}
