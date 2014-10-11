<?php
namespace Kozz\Tests\Model;

class MongoModelUserAddress extends \EMongoEmbeddedDocument
{
	public $city;
	public $street;
	public $apartment;
	public $zip;

	public function rules()
	{
		return array(
			array('city, street'  , 'length', 'max'=>255),
			array('apartment, zip', 'length', 'max'=>10),
		);
	}

	public function attributeLabels()
	{
		return array(
			'zip' => 'Postal Code',
		);
	}
}
