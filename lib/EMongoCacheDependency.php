<?php

/**
 * @author    Florian Fackler <darek.krk@gmail.com>
 * @copyright 2011 mintao GmbH & Co. KG
 * @license   New BSD license
 * @version   1.3
 * @category  ext
 * @package   ext.YiiMongoDbSuite
 */
class EMongoCacheDependency extends CCacheDependency
{

  /**
   * crit
   *
   * @var EMongoCriteria
   * @access private
   */
  private $crit;
  /**
   * collection
   *
   * @var EMongoDocument
   * @access private
   */
  private $collection;

  public function __construct(EMongoDocument $collection, EMongoCriteria $criteria)
  {
    $this->collection = $collection;
    $this->crit       = $criteria;
    $this->crit->limit(1);
  }

  /**
   * generateDependentData
   *
   * @access protected
   * @return void
   */
  protected function generateDependentData()
  {
    $res = $this->collection->find($this->crit);
    if (!$res instanceof $this->collection)
    {
      return null;
    }

    return md5(serialize($res->toArray()));
  }
}
