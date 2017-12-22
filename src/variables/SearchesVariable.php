<?php

namespace anecka\retsrabbit\variables;

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
		$search = craft()->retsRabbit_searches->getById($id);

		return !is_null($search);
	}

	public function criteria()
	{
		return new RetsRabbit_SearchCriteriaModel;
	}
}