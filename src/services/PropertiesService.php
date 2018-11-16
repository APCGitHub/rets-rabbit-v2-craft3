<?php /** @noinspection ALL */

namespace apc\retsrabbit\services;

use Apc\RetsRabbit\Core\ApiService;
use Apc\RetsRabbit\Core\Bridges\CraftBridge;
use Apc\RetsRabbit\Core\Resources\PropertiesResource;
use apc\retsrabbit\RetsRabbit;

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
        $bridge   = new CraftBridge;

        //Set the token fetcher function so the core lib can grab tokens
        //from cache on the plugin's behalf
        $bridge->setTokenFetcher(function() {
            return RetsRabbit::$plugin->getCache()->get('access_token');
        });

        //Load the Craft Bridge into the ApiService
        $this->api = new ApiService($bridge);

        //Allow developer to override base endpoint
        if ($settings->apiEndpoint) {
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

        if ($res->didFail() && RetsRabbit::$plugin->getApiResponses()->hasPermissionError($res)) {
            Craft::warning('A permission error occurred.', __METHOD__);

            $success = RetsRabbit::$plugin->getTokens()->refresh();

            if ($success !== null) {
                $res = $this->resource->search($params);
            } else {
                Craft::error('Could not refresh the token during a search.', __METHOD__);
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
        $res = $this->resource->single($id, $params ?? []);

        if ($res->didFail() && RetsRabbit::$plugin->getApiResponses()->hasPermissionError($res)) {
            Craft::warning('A permission error occurred.', __METHOD__);

            $success = RetsRabbit::$plugin->getTokens()->refresh();

            if ($success !== null) {
                $res = $this->resource->single($id, $params);
            } else {
                Craft::error('Could not refresh the token during property lookup.', __METHOD__);
            }
        }

        return $res;
    }
}
