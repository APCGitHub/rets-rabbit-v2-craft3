<?php

namespace anecka\retsrabbit\variables;

use anecka\retsRabbit\transformers\PropertyTransformer;
use anecka\retsRabbit\serializers\RetsRabbitArraySerializer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

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
	 * @return array
	 */
	public function find($id = '', $resoParams = array(), $useCache = false, $cacheDuration = null)
	{
		$cacheKey = md5($id . serialize($resoParams));
		$cacheKey = 'properties/' . hash('sha256', $cacheKey);
		$data = array();
		$error = false;

		//See if fetching from cache
		if($useCache) {
			$data = craft()->retsRabbit_cache->get($cacheKey);
		}

		//Check if any result pulled from cache
		if(is_null($data) || empty($data)) {
			$res = craft()->retsRabbit_properties->find($id, $resoParams);

			if(!$res->didSucceed()) {
				$error = true;
			} else {
				$data = $res->getResponse();

				if($useCache) {
					$ttl = $cacheDuration ?: $this->cacheDuration;

					craft()->retsRabbit_cache->set($cacheKey, $data, $ttl);
				}
			}
		}

		$viewData = null;

		if(!$error) {
			if(empty($data)) {
				$viewData = array();
			} else {
				$resources = new Item($data, new PropertyTransformer);
        		$viewData = $this->fractal->createData($resources)->toArray();
			}
		}

		return $viewData;
	}

	/**
	 * Perform a query against the Rets Rabbit API.
	 * 
	 * @param  $params array
	 * @param  $useCache bool
	 * @param  $cacheDuration mixed
	 * @return array
	 */
	public function query($params = array(), $useCache = false, $cacheDuration = null)
	{
		$cacheKey = 'searches/' . hash('sha256', serialize($params));
		$data = array();
		$error = false;

		//See if fetching from cache
		if($useCache) {
			$data = craft()->retsRabbit_cache->get($cacheKey);
		}

		//Check if any result pulled from cache
		if(is_null($data) || empty($data)) {
			$res = craft()->retsRabbit_properties->search($params);

			if(!$res->didSucceed()) {
				$error = true;
			} else {
				$data = $res->getResponse()['value'];

				if($useCache) {
					$ttl = $cacheDuration ?: $this->cacheDuration;

					craft()->retsRabbit_cache->set($cacheKey, $data, $ttl);
				}
			}
		}

		$viewData = null;

		if(!$error) {
			if(empty($data)) {
				$viewData = array();
			} else {
				$resources = new Collection($data, new PropertyTransformer);
        		$viewData = $this->fractal->createData($resources)->toArray();
			}
		}

		return $viewData;
	}

	/**
	 * Grab a saved search and run that search against the Rets Rabbit API
	 * 
	 * @param  string $id
	 * @param  bool $useCache
	 * @param  mixed $cacheDuration
	 * @return array
	 */
	public function search($id = '', $overrides = array(), $useCache = false, $cacheDuration = null)
	{
		$search = craft()->retsRabbit_searches->getById($id);

		if($search) {
			$currentPage = craft()->request->getPageNum();
			$mergeableKeys = array('$select', '$orderby', '$top');
			$params = $search->getAttribute('params');
			$params = json_decode($params, true);
			foreach($mergeableKeys as $key) {
				if(isset($overrides[$key])) {
					$params[$key] = $overrides[$key];
				}
			}
			if($currentPage > 1) {
				$params['$skip'] = ($currentPage - 1) * $params['$top'];
			}
			$cacheKey = 'searches/' . hash('sha256', serialize($params));
			$data = array();
			$error = false;

			//See if fetching from cache
			if($useCache) {
				$data = craft()->retsRabbit_cache->get($cacheKey);
			}

			if(is_null($data) || empty($data)) {
				$res = craft()->retsRabbit_properties->search($params);

				if(!$res->didSucceed()) {
					$error = true;
				} else {
					$data = $res->getResponse()['value'];

					if($useCache) {
						$ttl = $cacheDuration ?: $this->cacheDuration;

						craft()->retsRabbit_cache->set($cacheKey, $data, $ttl);
					}
				}
			}

			$viewData = null;

			if(!$error) {
				if(empty($data)) {
					$viewData = array();
				} else {
					$resources = new Collection($data, new PropertyTransformer);
	        		$viewData = $this->fractal->createData($resources)->toArray();
				}
			}

			return $viewData;
		} else {
			return null;
		}
	}
}
