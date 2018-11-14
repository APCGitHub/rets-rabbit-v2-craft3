<?php

namespace anecka\retsrabbit\models;

use craft\base\Model;

class Settings extends Model
{
	/**
	 * RR Client ID
	 * @var string
	 */
	public $clientId;

	/**
	 * RR Client Secret
	 * @var string
	 */
	public $clientSecret;

	/**
	 * Custom RR API Endpoint
	 * @var string
	 */
	public $apiEndpoint;

	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [
			[['clientId', 'clientSecret'], 'required']
		];
	}
}