<?php

namespace apc\retsrabbit\variables;

use apc\retsrabbit\models\Search;
use apc\retsrabbit\RetsRabbit;
use apc\retsrabbit\serializers\RetsRabbitArraySerializer;
use apc\retsrabbit\transformers\PropertyTransformer;
use apc\retsrabbit\ViewModel;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Craft;
use yii\web\View;

class PropertiesVariable
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * Cache duration in seconds
     *
     * @var integer
     */
    private $cacheDuration = 3600;

    /**
     * RetsRabbit_PropertiesVariable Constructor
     */
    public function __construct()
    {
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new RetsRabbitArraySerializer);
    }

    /**
     * Find a property listing by its MSL id.
     *
     * @param  $id string
     * @param array $resoParams
     * @param bool $useCache
     * @param null $cacheDuration
     * @return ViewModel
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function find($id = '', $resoParams = [], $useCache = false, $cacheDuration = null): ViewModel
    {
        $cacheKey = md5($id . serialize($resoParams));
        $cacheKey = 'properties/' . hash('sha256', $cacheKey);
        $data     = [];
        $error    = false;

        //See if fetching from cache
        if ($useCache) {
            $data = RetsRabbit::$plugin->getCache()->get($cacheKey);
        }

        //Check if any result pulled from cache
        if ($data === null || empty($data)) {
            $res = RetsRabbit::$plugin->getProperties()->find($id, $resoParams);

            if (!$res->didSucceed()) {
                $error = true;
                $data  = $res->getResponse();
            } else {
                $data = $res->getResponse();

                if ($useCache) {
                    $ttl = $cacheDuration ?: $this->cacheDuration;

                    RetsRabbit::$plugin->getCache()->set($cacheKey, $data, $ttl);
                }
            }
        }

        $viewModel = new ViewModel();

        if (!$error && !empty($data)) {
            $resources       = new Item($data, new PropertyTransformer);
            $viewModel->data = $this->fractal->createData($resources)->toArray();
        }

        return $viewModel;
    }

    /**
     * Perform a query against the Rets Rabbit API.
     *
     * @param  $params array
     * @param  $useCache bool
     * @param  $cacheDuration mixed
     * @return ViewModel
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function query($params = [], $useCache = false, $cacheDuration = null): ViewModel
    {
        $cacheKey  = 'searches/' . hash('sha256', serialize($params));
        $data      = [];
        $error     = false;
        $viewModel = new ViewModel();

        //See if fetching from cache
        if ($useCache) {
            $data = RetsRabbit::$plugin->getCache()->get($cacheKey);
        }

        //Check if any result pulled from cache
        if ($data === null || empty($data)) {
            $res = RetsRabbit::$plugin->getProperties()->search($params);

            if (!$res->didSucceed()) {
                $error = true;
            } else {
                $data = $res->getResponse()['value'];

                if ($useCache) {
                    $ttl = $cacheDuration ?: $this->cacheDuration;

                    RetsRabbit::$plugin->getCache()->set($cacheKey, $data, $ttl);
                }
            }
        }

        if (!$error && !empty($data)) {
            $resources       = new Collection($data, new PropertyTransformer);
            $viewModel->data = $this->fractal->createData($resources)->toArray();
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
     * @return ViewModel
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function search(
        $id = '', $overrides = [], $useCache = false, $cacheDuration = null
    ): ViewModel {
        /** @var Search $search */
        $search = RetsRabbit::$plugin->getSearches()->getById($id);

        if (!$search) {
            return null;
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
        $data     = [];
        $error    = false;

        //See if fetching from cache
        if ($useCache) {
            $data = RetsRabbit::$plugin->getCache()->get($cacheKey);
        }

        if ($data === null || empty($data)) {
            $res = RetsRabbit::$plugin->getProperties()->search($params);

            if (!$res->didSucceed()) {
                $error = true;
            } else {
                $data = $res->getResponse()['value'];

                if ($useCache) {
                    $ttl = $cacheDuration ?: $this->cacheDuration;

                    RetsRabbit::$plugin->getCache()->set($cacheKey, $data, $ttl);
                }
            }
        }

        $viewModel = new ViewModel();

        if (!$error && !empty($data)) {
            $resources       = new Collection($data, new PropertyTransformer);
            $viewModel->data = $this->fractal->createData($resources)->toArray();
        }

        return $viewModel;
    }
}
