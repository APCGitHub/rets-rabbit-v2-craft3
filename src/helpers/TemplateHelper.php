<?php

namespace apc\retsrabbit\helpers;

use apc\retsrabbit\viewmodels\MultipleListingsViewModel;
use Craft;
use craft\web\twig\variables\Paginate;

use apc\retsrabbit\models\SearchCriteriaModel;
use apc\retsrabbit\RetsRabbit;
use Exception;

class TemplateHelper
{
    /**
     * @param SearchCriteriaModel $criteria
     * @return array
     * @throws Exception
     */
    public static function paginateProperties(SearchCriteriaModel $criteria): array
    {
        if ($criteria->getSearchId() === null) {
            throw new Exception('The search id was not supplied.');
        }

        $paginateV   = new Paginate();
        $currentPage = Craft::$app->request->getPageNum();
        $search      = RetsRabbit::$plugin->getSearches()->getById($criteria->getSearchId());
        $viewModel   = new MultipleListingsViewModel();
        $ttl         = 3600;

        if ($search) {
            $savedSearchParams = json_decode($search->params, true);

            //Count total related variables
            $countParams   = array_merge($savedSearchParams, ['$select' => $criteria->countMethod]);
            $countCacheKey = 'pagination/' . hash('sha256', serialize($countParams));
            $total         = RetsRabbit::$plugin->getCache()->get($countCacheKey);
            $perPage       = $criteria->getPerPage();
            $countError    = false;

            //Search related variables
            $mergeableKeys       = ['$select', '$orderby'];
            $queryParams         = $savedSearchParams;
            $queryParams['$top'] = $perPage;
            $overrides           = $criteria->get();

            foreach ($mergeableKeys as $key) {
                if (isset($overrides[$key])) {
                    $queryParams[$key] = $overrides[$key];
                }
            }

            if ($currentPage > 1) {
                $queryParams['$skip'] = ($currentPage - 1) * $queryParams['$top'];
            }

            $searchCacheKey = 'searches/' . hash('sha256', serialize($queryParams));
            $listingData    = RetsRabbit::$plugin->getCache()->get($searchCacheKey);

            if ($total === false) {
                $res = RetsRabbit::$plugin->getProperties()->search($countParams);

                if ($res->wasSuccessful()) {
                    $total = $res->arrayBody()['@retsrabbit.total_results'] ?? 0;

                    RetsRabbit::$plugin->getCache()->set($countCacheKey, $total, $ttl);
                } else {
                    $countError = true;
                }
            }

            //Try to fetch the search results
            if ($listingData === false) {
                $res = RetsRabbit::$plugin->getProperties()->search($queryParams);

                if (!$res->wasSuccessful()) {
                    $viewModel->error = $res->error();
                } else {
                    $viewModel->decorateResource($res->listings());

                    RetsRabbit::$plugin->getCache()->set($searchCacheKey, $viewModel, $ttl);
                }
            } else {
                $viewModel = $listingData;
            }

            if ($countError || $viewModel->hasErrors() || $total == 0) {
                return [$paginateV, $viewModel];
            }

            if (!$perPage) {
                $perPage = $total;
            }

            $totalPages = ceil($total / $perPage);

            if ($totalPages == 0) {
                return [$paginateV, $viewModel];
            }

            if ($currentPage > $totalPages) {
                $currentPage = $totalPages;
            }

            $offset = $perPage * ($currentPage - 1);
            $last   = $offset + $perPage;

            if ($last > $total) {
                $last = $total;
            }

            $paginateV->first       = $offset + 1;
            $paginateV->last        = $last;
            $paginateV->total       = $total;
            $paginateV->currentPage = $currentPage;
            $paginateV->totalPages  = $totalPages;
        }

        return [$paginateV, $viewModel];
    }
}