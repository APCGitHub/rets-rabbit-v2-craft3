<?php

namespace anecka\retsrabbit\variables;

use anecka\retsrabbit\helpers\SearchCriteriaModel;
use anecka\retsrabbit\RetsRabbit;

class SearchesVariable
{
	/**
	 * See if a Rets Rabbit search exists.
	 * 
	 * @param  integer $id
	 * @return bool
	 */
	public function exists($id = 0)
	{
		$search = RetsRabbit::$plugin->searches->getById($id);

		return !is_null($search);
	}

	/**
	 * Get a search SearchCriteriaModel
	 * @return SearchCriteriaModel
	 */
	public function criteria()
	{
		return new SearchCriteriaModel;
	}
}