<?php

namespace anecka\retsrabbit\services;

use Craft;
use Exception;

use anecka\retsrabbit\exceptions\InvalidSearchException;
use anecka\retsrabbit\models\Search;
use anecka\retsrabbit\records\SearchRecord;
use craft\base\Component;

class SearchesService extends Component
{
	/**
	 * Create a new search model
	 * 
	 * @param  array
	 * @return Search
	 */
	public function newSearch($attributes = array())
	{
		$model = new Search();
		
		if(isset($attributes['params'])) {
			$attributes['params'] = json_encode($attributes['params']);
		}

		$model->setAttributes($attributes);

		return $model;
	}

	/**
	 * Create a new search model with a 'property' type
	 * 
	 * @param  array
	 * @return Search
	 */
	public function newPropertySearch($attributes = array())
	{
		$attributes['type'] = 'property';

		return $this->newSearch($attributes);
	}

	/**
	 * Save a search
	 * 
	 * @param  Search
	 * @return bool
	 */
	public function saveSearch(Search $model, bool $runValidation): bool
	{
		if($runValidation && $model->validate()) {
			Craft::info('Search not saved due to validation error.', __METHOD__);

			return false;
		}

		if($model->id) {
			$record = SearchRecord::find()
				->where(['id' => $model->id])
				->one();

			if(!$record) {
				throw new InvalidSearchException("No search exists with the ID '{$model->id}'");
			}

			$isNewSearch = false;
		} else {
			$record = new SearchRecord;
			$isNewSearch = false;
		}

		$record->setAttributes($model->getAttributes());

		$transaction = Craft::$app->getDb()->beginTransaction();

		try {
			$record->save();

			if($isNewSearch) {
				$model->id = $record->id;
			}

			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollBack();

			throw $e;
		}

		return true;

	}

	/**
	 * Find a search by id
	 * 
	 * @param  $id integer
	 * @return Search|null
	 */
	public function getById($id = 0)
	{
		$record = SearchRecord::findOne($id);

		if($record) {
			return new Search($record->toArray());
		}

		return null;
	}

	/**
	 * Delete a search by id
	 * 
	 * @param  $id integer
	 * @return bool
	 */
	public function deleteById($id = 0): bool
	{
		$record = SearchRecord::findOne($id);

		if(!$record) {
			return true;
		}

		$record->delete();

		return true;
	}
}