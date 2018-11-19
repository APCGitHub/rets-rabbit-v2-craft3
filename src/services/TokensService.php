<?php

namespace apc\retsrabbit\services;

use Apc\RetsRabbit\Core\RetsRabbitApi;
use apc\retsrabbit\RetsRabbit;

use Craft;
use craft\base\Component;
use Yii;


class TokensService extends Component
{
    /**
     * @var RetsRabbitApi
     */
    private $api;

    /**
     * Constructor
     *
     * @param $config
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->api = Yii::$container->get('retsRabbitApi');
    }

    /**
     * Try to fetch a new access token from the RR API.
     *
     * @return mixed|null
     */
    public function refresh()
    {
        $token = null;
        $res   = $this->api->accessToken()->create([
            'grant_type'    => 'client_credentials',
            'client_id'     => RetsRabbit::$plugin->getSettings()->clientId,
            'client_secret' => RetsRabbit::$plugin->getSettings()->clientSecret
        ]);

        if ($res->wasSuccessful()) {
            $token = $res->token();
            RetsRabbit::$plugin->getCache()->set('access_token', $token->access_token, $token->expires_in);
        } else {
            Craft::warning('Could not fetch the access token.', __METHOD__);
        }

        return $token;
    }

    /**
     * @return boolean
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function isValid(): bool
    {
        $token = RetsRabbit::$plugin->getCache()->get('access_token');

        return !($token === null || empty($token));
    }
}