<?php


namespace apc\retsrabbit\services;

use Apc\RetsRabbit\Core\Responses\MultipleOpenHouseResponse;
use Apc\RetsRabbit\Core\Responses\SingleOpenHouseResponse;
use Apc\RetsRabbit\Core\RetsRabbitApi;

use Apc\RetsRabbit\Core\TransferObjects\AccessToken;
use apc\retsrabbit\RetsRabbit;
use apc\retsrabbit\Traits\ApiCredentials;
use Craft;
use craft\base\Component;

class OpenHousesService extends Component
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
     * @param $id
     * @param array $params
     * @return SingleOpenHouseResponse
     * @throws \apc\retsrabbit\exceptions\MissingClientCredentials
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function find($id, $params = []): SingleOpenHouseResponse
    {
        $this->checkCredentials();

        $token = RetsRabbit::$plugin->getCache()->get('access_token');
        $res = $this->api->openHouse()->single($id, $params, [
            'Authorization' => 'Bearer ' . $token
        ]);

        if (!$res->wasSuccessful() && $res->error()->code === 'permission') {
            Craft::warning('A permission error occurred.', __METHOD__);

            /** @var AccessToken $token */
            $token = RetsRabbit::$plugin->getTokens()->refresh();

            if ($token !== null && $token->access_token) {
                $res = $this->api->openHouse()->single($id, $params, [
                    'Authorization' => 'Bearer ' . $token->access_token
                ]);
            } else {
                Craft::error('Could not refresh the token during open house lookup.', __METHOD__);
            }
        }

        return $res;
    }

    /**
     * @param array $params
     * @return MultipleOpenHouseResponse
     * @throws \apc\retsrabbit\exceptions\MissingClientCredentials
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params = []): MultipleOpenHouseResponse
    {
        $this->checkCredentials();

        $token = RetsRabbit::$plugin->getCache()->get('access_token');
        $res   = $this->api->openHouse()->search($params, [
            'Authorization' => 'Bearer ' . $token
        ]);

        if (!$res->wasSuccessful() && $res->error()->code === 'permission') {
            Craft::warning('A permission error occurred.', __METHOD__);

            /** @var AccessToken $token */
            $token = RetsRabbit::$plugin->getTokens()->refresh();

            if ($token !== null && $token->access_token !== null) {
                $res = $this->api->openHouse()->search($params, [
                    'Authorization' => 'Bearer ' . $token->access_token
                ]);
            } else {
                Craft::error('Could not refresh the token during a search.', __METHOD__);
            }
        }

        return $res;
    }
}