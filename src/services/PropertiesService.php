<?php /** @noinspection ALL */

namespace anecka\retsrabbit\services;

use Anecka\RetsRabbit\Core\ApiService;
use Anecka\RetsRabbit\Core\Bridges\CraftBridge;
use Anecka\RetsRabbit\Core\Resources\PropertiesResource;
use anecka\retsrabbit\RetsRabbit;

use Craft;
use craft\base\Component;

class PropertiesService extends Component
{
	/**
	 * The api service from the core RR library
	 * 
	 * @var ApiService
	 */
	private $api;

	/**
	 * The properties resource endpoint
	 * 
	 * @var PropertiesResource
	 */
	private $resource;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$settings = RetsRabbit::$plugin->getSettings();
		$bridge = new CraftBridge;

		//Set the token fetcher function so the core lib can grab tokens
		//from cache on the plugin's behalf
		$bridge->setTokenFetcher(function () {
			return RetsRabbit::$plugin->getCache()->get('access_token', false);
		});

		//Load the Craft Bridge into the ApiService
		$this->api = new ApiService($bridge);

		//Allow developer to override base endpoint
		if($settings->apiEndpoint) {
			$this->api->overrideBaseApiEndpoint($settings->apiEndpoint);
		}

		//Instantiate the PropertiesResource
		$this->resource = new PropertiesResource($this->api);
	}

	/**
	 * @param  array
	 * @return array
	 */
	public function search($params = [])
	{
		$res = $this->resource->search($params);

		if($res->didFail()) {
			$contents = $res->getResponse();

			if(isset($contents['error']) && isset($contents['error']['code'])) {
				Craft::warning('A permission error occurred.', __METHOD__);
				
				$code = $contents['error']['code'];

				if($code == 'permission') {
					$success = RetsRabbit::$plugin->getTokens()->refresh();

					if(!is_null($success)) {
						$res = $this->resource->search($params);
					} else {
						Craft::error('Could not refresh the token during a search.', __METHOD__);
					}
				}
			}
		}

		return $res;
	}

	/**
	 * @param  string
	 * @return array
	 */
	public function find($id = '', $params = [])
	{
		$res = $this->resource->single($id, $params);

		if($res->didFail()) {
			$contents = $res->getResponse();

			if(isset($contents['error']) && isset($contents['error']['code'])) {
				Craft::warning('A permission error occurred.', __METHOD__);
				
				$code = $contents['error']['code'];

				if($code == 'permission') {
					$success = RetsRabbit::$plugin->getTokens()->refresh();

					if(!is_null($success)) {
						$res = $this->resource->single($id, $params);
					} else {
						Craft::error('Could not refresh the token during property lookup.', __METHOD__);
					}
				}
			}
		}

		return $res;
	}
}
