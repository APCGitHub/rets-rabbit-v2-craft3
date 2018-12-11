<?php

namespace apc\retsrabbit\variables;

use apc\retsrabbit\models\SearchCriteriaModel;
use apc\retsrabbit\RetsRabbit;

class SearchesVariable
{
	/**
	 * See if a Rets Rabbit search exists.
	 * 
	 * @param  integer $id
	 * @return bool
	 */
	public function exists($id = 0): bool
	{
		$search = RetsRabbit::$plugin->getSearches()->getById($id);

		return $search !== null;
	}

	/**
	 * Get a search SearchCriteriaModel
	 * @return SearchCriteriaModel
	 */
	public function criteria(): SearchCriteriaModel
	{
		return new SearchCriteriaModel;
	}
}