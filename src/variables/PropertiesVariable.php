<?php

namespace apc\retsrabbit\variables;

use apc\retsrabbit\models\Search;
use apc\retsrabbit\RetsRabbit;
use apc\retsrabbit\viewmodels\MultipleListingsViewModel;
use apc\retsrabbit\viewmodels\SingleListingViewModel;
use apc\retsrabbit\viewmodels\ViewModel;
use Craft;

class PropertiesVariable
{
    /**
     * Cache duration in seconds
     *
     * @var integer
     */
    private $cacheDuration = 3600;

    /**
     * Find a property listing by its MSL id.
     *
     * @param  $id string
     * @param array $resoParams
     * @param bool $useCache
     * @param null $cacheDuration
     * @return \apc\retsrabbit\viewmodels\ViewModel
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function find($id = '', $resoParams = [], $useCache = false, $cacheDuration = null): ViewModel
    {
        $resoParams = $resoParams ?? [];
        $cacheKey   = md5($id . serialize($resoParams));
        $cacheKey   = 'properties/' . hash('sha256', $cacheKey);

        if ($useCache) {
            /** @var \apc\retsrabbit\viewmodels\ViewModel $viewModel */
            $viewModel = RetsRabbit::$plugin->getCache()->get($cacheKey);

            if ($viewModel !== false) {
                return $viewModel;
            }
        }

        $viewModel = new SingleListingViewModel();

        $res = RetsRabbit::$plugin->getProperties()->find($id, $resoParams);

        if (!$res->wasSuccessful()) {
            $viewModel->error = $res->error();
        } else {
            $viewModel->decorateResource($res->listing());

            if ($useCache) {
                $ttl = $cacheDuration ?: $this->cacheDuration;

                RetsRabbit::$plugin->getCache()->set($cacheKey, $viewModel, $ttl);
            }
        }

        return $viewModel;
    }

    /**
     * Perform a query against the Rets Rabbit API.
     *
     * @param  $params array
     * @param  $useCache bool
     * @param  $cacheDuration mixed
     * @return \apc\retsrabbit\viewmodels\ViewModel
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function query($params = [], $useCache = false, $cacheDuration = null): ViewModel
    {
        $params   = $params ?? [];
        $cacheKey = 'searches/' . hash('sha256', serialize($params));

        if ($useCache) {
            /** @var MultipleListingsViewModel $viewModel */
            $viewModel = RetsRabbit::$plugin->getCache()->get($cacheKey);

            if ($viewModel !== false) {
                return $viewModel;
            }
        }

        $viewModel = new MultipleListingsViewModel();
        $res       = RetsRabbit::$plugin->getProperties()->search($params);

        if (!$res->wasSuccessful()) {
            $viewModel->error = $res->error();
        } else {
            $viewModel->decorateResource($res->listings());

            if ($useCache) {
                $ttl = $cacheDuration ?: $this->cacheDuration;

                RetsRabbit::$plugin->getCache()->set($cacheKey, $viewModel, $ttl);
            }
        }

        return $viewModel;
    }

    /**
     * Grab a saved search and run that search against the Rets Rabbit API
     *
     * @param  string $id
     * @param array $overrides
     * @param  bool $useCache
     * @param  mixed $cacheDuration
     * @return MultipleListingsViewModel
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function search(
        $id = '', $overrides = [], $useCache = false, $cacheDuration = null
    ): MultipleListingsViewModel {
        /** @var Search $search */
        $search = RetsRabbit::$plugin->getSearches()->getById($id);

        if (!$search) {
            return new MultipleListingsViewModel();
        }

        $currentPage   = Craft::$app->request->getPageNum();
        $mergeableKeys = ['$select', '$orderby', '$top'];
        $params        = $search->params;
        $params        = json_decode($params, true);
        foreach ($mergeableKeys as $key) {
            if (isset($overrides[$key])) {
                $params[$key] = $overrides[$key];
            }
        }
        if ($currentPage > 1) {
            $params['$skip'] = ($currentPage - 1) * $params['$top'];
        }
        $cacheKey = 'searches/' . hash('sha256', serialize($params));

        if ($useCache) {
            $viewModel = RetsRabbit::$plugin->getCache()->get($cacheKey);

            if ($viewModel !== false) {
                return $viewModel;
            }
        }

        $res       = RetsRabbit::$plugin->getProperties()->search($params);
        $viewModel = new MultipleListingsViewModel();

        if (!$res->wasSuccessful()) {
            $viewModel->error = $res->error();
        } else {
            $viewModel->decorateResource($res->listings());

            if ($useCache) {
                $ttl = $cacheDuration ?: $this->cacheDuration;

                RetsRabbit::$plugin->getCache()->set($cacheKey, $viewModel, $ttl);
            }
        }

        return $viewModel;
    }
}
