<?php

/**
 * @author    Ianaré Sévi
 * @author    Dariusz Górecki <darek.krk@gmail.com>
 * @author    Invenzzia Group, open-source division of CleverIT company http://www.invenzzia.org
 * @copyright 2011 CleverIT http://www.cleverit.com.pl
 * @license   New BSD license
 * @version   1.3
 */

/**
 * Class EEmbeddedArraysBehavior
 *
 *
 * @method EMongoEmbeddedDocument getOwner()
 */
class EEmbeddedArraysBehavior extends EMongoDocumentBehavior
{
  /**
   * Name of property witch holds array od documents
   *
   * @var string $arrayPropertyName
   * @since v1.0
   */
  public $arrayPropertyName;

  /**
   * Class name of doc in array
   *
   * @var string $arrayDocClassName
   * @since v1.0
   */
  public $arrayDocClassName;

  /**
   * @var array|EMongoEmbeddedDocument[]
   */
  private $_cache;

  /**
   * This flag shows us if we're connected to an embedded document
   *
   * @var boolean $_embeddedOwner
   */
  private $_embeddedOwner;

  public function events()
  {
    if (!$this->_embeddedOwner)
    {
      return parent::events();
    }

    // If attached to an embedded document these events are not defined
    // and would throw an error if attached to
    $events = parent::events();
    unset($events['onBeforeSave']);
    unset($events['onAfterSave']);
    unset($events['onBeforeDelete']);
    unset($events['onAfterDelete']);
    unset($events['onBeforeFind']);
    unset($events['onAfterFind']);

    return $events;
  }

  /**
   * @since v1.0
   * @see   CBehavior::attach()
   */
  public function attach($owner)
  {
    // Test if we have correct embding class
    if (!is_subclass_of($this->arrayDocClassName, 'EMongoEmbeddedDocument'))
    {
      throw new EMongoException(
        Yii::t('yii', $this->arrayDocClassName . ' is not a child class of EMongoEmbeddedDocument.')
      );
    }

    $this->_embeddedOwner = !($owner instanceof EMongoDocument);

    parent::attach($owner);

    $this->parseExistingArray();
  }

  /**
   * Event: initialize array of embded documents
   *
   * @since v1.0
   *
   * @param $event
   */
  public function afterEmbeddedDocsInit($event)
  {
    $this->parseExistingArray();
  }

  /**
   * @since v1.0.2
   *
   * @param CEvent $event
   */
  public function afterValidate($event)
  {
    parent::afterValidate($event);

    foreach ($this->getProperty() as $doc)
    {
      if (!$doc->validate())
      {
        $this->getOwner()->addErrors($doc->getErrors());
      }
    }
  }

  public function beforeToArray($event)
  {
    if(!$this->checkProperty())
    {
      return false;
    }

    $arrayOfDocs = array();
    $this->_cache = $this->getProperty();

    foreach ($this->_cache as $doc)
    {
      $arrayOfDocs[] = $doc->toArray();
    }

    $this->setProperty($arrayOfDocs);

    return true;
  }

  /**
   * Event: re-initialize array of embedded documents which where toArray()ized by beforeSave()
   *
   * @param $event
   */
  public function afterToArray($event)
  {
    $this->setProperty($this->_cache);
    $this->_cache = null;
  }

  /**
   * @since v1.0
   */
  private function parseExistingArray()
  {
    if(!$this->checkProperty())
    {
      return;
    }

    $arrayOfDocs = [];
    foreach ($this->getProperty() as $doc)
    {
      $arrayOfDocs[] = $this->processSingleEmbed($doc);
    }
    $this->setProperty($arrayOfDocs);

  }

  private function processSingleEmbed(array $doc)
  {
    /** @var EMongoEmbeddedDocument $obj */
    $obj = new $this->arrayDocClassName;
    $obj->setAttributes($doc, false);
    $obj->setOwner($this->getOwner());

    foreach ($obj->behaviors() as $name => $value)
    {
      $behavior = $obj->asa($name);
      if ($behavior instanceof EEmbeddedArraysBehavior)
      {
        $behavior->parseExistingArray();
      }
    }
    return $obj;
  }

  /**
   * @return bool
   */
  private function checkProperty()
  {
    return is_array($this->getProperty());
  }

  /**
   * @return array|EMongoEmbeddedDocument[]
   */
  private function getProperty()
  {
    return $this->getOwner()->{$this->arrayPropertyName};
  }

  /**
   * @param array $value
   */
  private function setProperty($value)
  {
    $this->getOwner()->{$this->arrayPropertyName} = $value;
  }
}
