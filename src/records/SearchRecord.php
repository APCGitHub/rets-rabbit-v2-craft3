<?php

namespace anecka\retsrabbit\records;

use Craft;

use anecka\retsrabbit\RetsRabbit;
use craft\db\ActiveRecord;

class SearchRecord extends ActiveRecord
{
	/**
	 * @return string
	 */
	public static function tableName()
	{
		return '{{%rets_rabbit_searches}}';
	}

	/**
	 * @return array
	 */
	public function defineAttributes()
	{
		return array(
			'type' => array(
				AttributeType::String, 
				'required' => true, 
				'default' => 'property'
			),
			'params' => array(
				AttributeType::String, 
				'column' => ColumnType::Text, 
				'required' => true
			)
		);
	}
}