<?php

namespace anecka\retsrabbit\helpers;

class RetsRabbit_SearchCriteriaModel
{
	/**
	 * @var array
	 */
	private $params = array(
		'$select' => null,
		'$filter' => null,
		'$orderby' => null,
		'$skip' => null
	);

	/**
	 * @var null
	 */
	private $searchId = null;

	/**
	 * @var null
	 */
	private $limit = null;

	/**
	 * @var string
	 */
	public $countMethod = 'estimated_results';

	/**
	 * @param  integer $searchId
	 * @return $this
	 */
	public function forId($searchId = 0)
	{
		$this->searchId = $searchId;

		return $this;
	}

	/**
	 * @param  array $selects
	 * @return $this
	 */
	public function select($selects = array())
	{
		$s = is_array($selects) ? $selects : func_get_args();
		$s = implode(', ', $s);

		$this->params['$select'] = $s;

		return $this;
	}

	/**
	 * @param  string $filter
	 * @return $this
	 */
	public function filter($filter = '')
	{
		$this->params['$filter'] = $filter;

		return $this;
	}

	/**
	 * @param  integer $limit
	 * @return $this
	 */
	public function limit($limit = 0)
	{
		if($limit > 0)
			$this->limit = $limit;

		return $this;
	}

	/**
	 * @param  integer $skip
	 * @return $this
	 */
	public function skip($skip = 0)
	{
		if($skip > 0)
			$this->params['$skip'] = $skip;

		return $this;
	}

	/**
	 * @param  string $field
	 * @param  string $dir
	 * @return $this
	 */
	public function orderBy($field, $dir = 'asc')
	{
		if($dir != 'desc') {
			$dir = 'asc';
		}

		$this->params['$orderby'] = "$field $dir";

		return $this;
	}

	/**
	 * @param  string $method
	 * @return $this
	 */
	public function countBy($method)
	{
		if($method == 'exact') {
			$this->countMethod = 'total_results';
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function get()
	{
		return array_filter($this->params, function ($val) {
			return !is_null($val);
		});
	}

	/**
	 * @return int|null
	 */
	public function getPerPage()
	{
		return $this->limit;
	}

	/**
	 * @return int
	 */
	public function getSearchId()
	{
		return $this->searchId;
	}
}