<?php

namespace anecka\retsrabbit\helpers;

use Craft;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

use anecka\retsrabbit\models\SearchCriteriaModel;
use anecka\retsrabbit\RetsRabbit;
use anecka\retsrabbit\serializers\RetsRabbitArraySerializer;
use anecka\retsrabbit\transformers\PropertyTransformer;

class TemplateHelper
{
	/**
	 * @param  integer $searchId
	 * @param  array $resoParams
	 * @return array
	 */
	public static function paginateProperties(SearchCriteriaModel $criteria)
	{
		if(is_null($criteria->getSearchId())) {
			throw new \Exception("The search id was not supplied.");
		}

		$paginateV = new PaginateVariable;
		$currentPage = Craft::$app->request->getPageNum();
		$search = RetsRabbit::$plugin->searches->getById($criteria->getSearchId());
		$viewData = null;
		$fractal = new Manager();
		$fractal->setSerializer(new RetsRabbitArraySerializer);

		if($search) {
			$savedSearchParams = json_decode($search->params, true);

			//Count total related variables
			$countParams = array_merge($savedSearchParams, array('$select' => $criteria->countMethod));
			$countCacheKey = 'pagination/' . hash('sha256', serialize($countParams));
			$total = RetsRabbit::$plugin->cache->get($countCacheKey);
			$perPage = $criteria->getPerPage();
			$countError = false;

			//Search related variables
			$mergeableKeys = array('$select', '$orderby');
			$queryParams = $savedSearchParams;
			$queryParams['$top'] = $perPage;
			$overrides = $criteria->get();
			$data = array();
			$searchError = false;

			foreach($mergeableKeys as $key) {
				if(isset($overrides[$key])) {
					$queryParams[$key] = $overrides[$key];
				}
			}

			if($currentPage > 1) {
				$queryParams['$skip'] = ($currentPage - 1) * $queryParams['$top'];
			}

			$searchCacheKey = 'searches/' . hash('sha256', serialize($queryParams));
			$data = RetsRabbit::$plugin->cache->get($searchCacheKey);
			

			//Try to fetch the search total
			if(is_null($total) || $total === FALSE) {
				$res = RetsRabbit::$plugin->properties->search($countParams);

				if(!$res->didSucceed()) {
					$countError = true;
				} else {
					$total = $res->getResponse()['@retsrabbit.total_results'];

					RetsRabbit::$plugin->cache->set($countCacheKey, $total, 3600);
				}
			}

			//Try to fetch the search results
			if(is_null($data) || empty($data)) {
				$res = RetsRabbit::$plugin->properties->search($queryParams);

				if(!$res->didSucceed()) {
					$searchError = true;
				} else {
					$data = $res->getResponse()['value'];

					RetsRabbit::$plugin->cache->set($searchCacheKey, $data, 3600);
				}
			}

			if(!$searchError) {
				if(empty($data)) {
					$viewData = array();
				} else {
					$resources = new Collection($data, new PropertyTransformer);
	        		$viewData = $fractal->createData($resources)->toArray();
				}
			}

			if($countError || $searchError || $total == 0) {
				return array($paginateV, $viewData);
			}

			if(!$perPage) {
				$perPage = $total;
			}

			$totalPages = ceil($total / $perPage);

			if($totalPages == 0) {
				return array($paginateV, $viewData);
			}

			if ($currentPage > $totalPages) {
				$currentPage = $totalPages;
			}

			$offset = $perPage * ($currentPage - 1);
			$last = $offset + $perPage;

			if($last > $total) {
				$last = $total;
			}

			$paginateV->first = $offset + 1;
			$paginateV->last = $last;
			$paginateV->total = $total;
			$paginateV->currentPage = $currentPage;
			$paginateV->totalPages = $totalPages;
		}

		return array($paginateV, $viewData);
	}
}