<?php
/**
 * Rets Rabbit plugin for Craft CMS 3.x
 *
 * Display real estate listings in your craft site in a simple and intuitive way.
 *
 * @link      http://anecka.com
 * @copyright Copyright (c) 2017 Anecka, LLC
 */

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