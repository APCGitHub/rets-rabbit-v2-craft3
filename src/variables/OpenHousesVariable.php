<?php


namespace apc\retsrabbit\variables;


use Apc\RetsRabbit\Core\Responses\MultipleOpenHouseResponse;
use apc\retsrabbit\exceptions\MissingClientCredentials;
use apc\retsrabbit\RetsRabbit;
use apc\retsrabbit\viewmodels\MultipleOpenHousesViewModel;
use apc\retsrabbit\viewmodels\SingleOpenHouseViewModel;
use apc\retsrabbit\viewmodels\ViewModel;

class OpenHousesVariable
{
    /**
     * Cache duration in seconds
     *
     * @var integer
     */
    private $cacheDuration = 3600;

    /**
     * @param string $id
     * @param array $resoParams
     * @param bool $useCache
     * @param null $cacheDuration
     * @return SingleOpenHouseViewModel|ViewModel
     * @throws MissingClientCredentials
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function find($id = '', $resoParams = [], $useCache = false, $cacheDuration = null)
    {
        $resoParams = $resoParams ?? [];
        $cacheKey   = md5($id . serialize($resoParams));
        $cacheKey   = 'open-house/' . hash('sha256', $cacheKey);

        if ($useCache) {
            /** @var ViewModel $viewModel */
            $viewModel = RetsRabbit::$plugin->getCache()->get($cacheKey);

            if ($viewModel !== false) {
                return $viewModel;
            }
        }

        $viewModel = new SingleOpenHouseViewModel();
        $res = RetsRabbit::$plugin->getOpenHouses()->find($id, $resoParams);

        if(!$res->wasSuccessful()) {
            $viewModel->error = $res->error();
        } else {
            $viewModel->decorateResource($res->openHouse());

            if($useCache) {
                $ttl = $cacheDuration ?: $this->cacheDuration;

                RetsRabbit::$plugin->getCache()->set($cacheKey, $viewModel, $ttl);
            }
        }

        return $viewModel;
    }

    /**
     * @param array $params
     * @param bool $useCache
     * @param null $cacheDuration
     * @return ViewModel
     * @throws MissingClientCredentials
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function query($params = [], $useCache = false, $cacheDuration = null): ViewModel
    {
        $params   = $params ?? [];
        $cacheKey = 'searches/' . hash('sha256', serialize($params));

        if ($useCache) {
            /** @var MultipleOpenHousesViewModel $viewModel */
            $viewModel = RetsRabbit::$plugin->getCache()->get($cacheKey);

            if ($viewModel !== false) {
                return $viewModel;
            }
        }

        $viewModel = new MultipleOpenHousesViewModel();
        $res       = RetsRabbit::$plugin->getOpenHouses()->search($params);

        if (!$res->wasSuccessful()) {
            $viewModel->error = $res->error();
        } else {
            $viewModel->decorateResource($res->openHouses());

            if ($useCache) {
                $ttl = $cacheDuration ?: $this->cacheDuration;

                RetsRabbit::$plugin->getCache()->set($cacheKey, $viewModel, $ttl);
            }
        }

        return $viewModel;
    }
}