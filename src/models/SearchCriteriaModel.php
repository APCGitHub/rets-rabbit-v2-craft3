<?php

namespace apc\retsrabbit\models;

use craft\base\Model;

class SearchCriteriaModel extends Model
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
	public function forId($searchId = 0): self
    {
		$this->searchId = $searchId;

		return $this;
	}

	/**
	 * @param  array $selects
	 * @return $this
	 */
	public function select($selects = array()): self
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
	public function filter($filter = ''): self
	{
		$this->params['$filter'] = $filter;

		return $this;
	}

	/**
	 * @param  integer $limit
	 * @return $this
	 */
	public function limit($limit = 0): self
	{
		if($limit > 0) {
            $this->limit = $limit;
        }

		return $this;
	}

	/**
	 * @param  integer $skip
	 * @return $this
	 */
	public function skip($skip = 0): self
	{
		if($skip > 0) {
            $this->params['$skip'] = $skip;
        }

		return $this;
	}

	/**
	 * @param  string $field
	 * @param  string $dir
	 * @return $this
	 */
	public function orderBy($field, $dir = 'asc'): self
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
	public function countBy($method): self
	{
		if($method == 'exact') {
			$this->countMethod = 'total_results';
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function get(): array
	{
		return array_filter($this->params, function ($val) {
			return $val !== null;
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
	 * @return mixed
	 */
	public function getSearchId()
	{
		return $this->searchId;
	}
}