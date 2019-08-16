<?php /** @noinspection ALL */

namespace apc\retsrabbit\services;

use Apc\RetsRabbit\Core\ApiService;
use Apc\RetsRabbit\Core\Bridges\CraftBridge;
use Apc\RetsRabbit\Core\Resources\PropertiesResource;
use Apc\RetsRabbit\Core\Responses\MultipleListingResponse;
use Apc\RetsRabbit\Core\Responses\SingleListingResponse;
use Apc\RetsRabbit\Core\RetsRabbitApi;
use Apc\RetsRabbit\Core\TransferObjects\AccessToken;
use apc\retsrabbit\exceptions\MissingClientCredentials;
use apc\retsrabbit\RetsRabbit;

use apc\retsrabbit\Traits\ApiCredentials;
use Craft;
use craft\base\Component;

class PropertiesService extends Component
{
    use ApiCredentials;

    /**
     * The api service from the core RR library
     *
     * @var RetsRabbitApi
     */
    private $api;

    /**
     * Constructor
     */
    public function __construct()
    {
        /** @var RetsRabbitApi api */
        $this->api = \Yii::$container->get('retsRabbitApi');
    }

    /**
     * @param  array
     * @return MultipleListingResponse
     */
    public function search($params = []): MultipleListingResponse
    {
        $this->checkCredentials();

        $token = RetsRabbit::$plugin->getCache()->get('access_token');
        $res   = $this->api->property()->search($params, [
            'Authorization' => 'Bearer ' . $token
        ]);

        if (!$res->wasSuccessful() && $res->error()->code === 'permission') {
            Craft::warning('A permission error occurred.', __METHOD__);

            /** @var AccessToken $token */
            $token = RetsRabbit::$plugin->getTokens()->refresh();

            if ($token !== null && $token->access_token !== null) {
                $res = $this->api->property()->search($params, [
                    'Authorization' => 'Bearer ' . $token->access_token
                ]);
            } else {
                Craft::error('Could not refresh the token during a search.', __METHOD__);
            }
        }

        return $res;
    }

    /**
     * @param  string
     * @return SingleListingResponse
     */
    public function find($id = '', $params = []): SingleListingResponse
    {
        $this->checkCredentials();

        $token = RetsRabbit::$plugin->getCache()->get('access_token');
        $res   = $this->api->property()->single($id, $params, [
            'Authorization' => 'Bearer ' . $token
        ]);

        if (!$res->wasSuccessful() && $res->error()->code === 'permission') {
            Craft::warning('A permission error occurred.', __METHOD__);

            /** @var AccessToken $token */
            $token = RetsRabbit::$plugin->getTokens()->refresh();

            if ($token !== null && $token->access_token) {
                $res = $this->api->property()->single($id, $params, [
                    'Authorization' => 'Bearer ' . $token->access_token
                ]);
            } else {
                Craft::error('Could not refresh the token during property lookup.', __METHOD__);
            }
        }

        return $res;
    }
}
