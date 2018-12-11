<?php

namespace apc\retsrabbit\models;

use craft\base\Model;

/**
 * Search Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author APC, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 */
class Search extends Model
{
    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $siteId;

	/**
	 * The type of search
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * The search params
	 * 
	 * @var string
	 */
	public $params;

    /**
     * @var
     */
	public $dateCreated, $dateUpdated, $uid;

	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [
		    ['siteId', 'integer'],
			[['type', 'params'], 'string'],
			['type', 'required'],
            [['siteId', 'params', 'type'], 'safe']
		];
	}
}