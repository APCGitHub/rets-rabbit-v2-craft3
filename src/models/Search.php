<?php

namespace anecka\retsrabbit\models;

use craft\base\Model;

/**
 * Search Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Anecka, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 */
class Search extends Model
{
	/**
	 * The type of search
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * The search params
	 * 
	 * @var array
	 */
	public $params;

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			['type', 'string'],
			['type', 'required']
		];
	}
}