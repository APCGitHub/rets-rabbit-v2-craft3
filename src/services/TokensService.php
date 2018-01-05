<?php

namespace anecka\retsrabbit\services;

use Anecka\RetsRabbit\Core\ApiService;
use Anecka\RetsRabbit\Core\Bridges\CraftBridge;
use anecka\retsrabbit\RetsRabbit;

use Craft;
use craft\base\Component;


class TokensService extends Component
{
	/**
	 * @var ApiService
	 */
	private $api;

	/** 
	 * @var mixed
	 */
	private $settings;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$bridge = new CraftBridge;
		$this->api = new ApiService($bridge);
		$this->settings = RetsRabbit::$plugin->getSettings();

		//Allow developer to override base endpoint
		if($this->settings->apiEndpoint) {
			$this->api->overrideBaseApiEndpoint($settings->apiEndpoint);
		}
	}

	/**
	 * Try to fetch a new access token from the RR API.
	 * 
	 * @return mixed|null
	 */
	public function refresh()
	{
		$token = null;

		try {
			$res = $this->api->getAccessToken([
	      		'client_id' => $this->settings->clientId,
	      		'client_secret' => $this->settings->clientSecret
	    	]);

	        if($res->didSucceed()) {
	            $content = $res->getResponse();
	            $token = $content['access_token'];
	            $ttl = $content['expires_in'];

	            RetsRabbit::$plugin->cache->set('access_token', $token, $ttl, true);
	        } else {
	        	Craft::warning('Could not fetch the access token.', __METHOD__);
	        }
		} catch (\Exception $e) {
			Craft::error($e->getMessage(), __METHOD__);
		}

        return $token;
	}

	/**
	 * @return boolean
	 */
	public function isValid()
	{
		$token = RetsRabbit::$plugin->cache->get('access_token', true);

        if(is_null($token) || empty($token))
            return false;

        return true;
	}
}