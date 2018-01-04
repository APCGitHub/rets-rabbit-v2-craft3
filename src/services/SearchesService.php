<?php

namespace anecka\retsrabbit\services;

use Craft;

use craft\base\Component;

class SearchesService extends Component
{
	/**
	 * Create a new search model
	 * 
	 * @param  array
	 * @return BaseModel
	 */
	public function newSearch($attributes = array())
	{
		$model = new RetsRabbit_SearchModel();
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
	 * @return BaseModel
	 */
	public function newPropertySearch($attributes = array())
	{
		$attributes['type'] = 'property';

		return $this->newSearch($attributes);
	}

	/**
	 * Save a search
	 * 
	 * @param  RetsRabbit_SearchModel
	 * @return bool
	 */
	public function saveSearch(RetsRabbit_SearchModel &$model)
	{
		if($id = $model->getAttribute('id')) {
			if (null === ($record = RetsRabbit_SearchRecord::model()->findById($id))) {
                throw new Exception(Craft::t('rets-rabbit', Can\'t find search with ID "{id}"', array('id' => $id)));
            }
		} else {
			$record = new RetsRabbit_SearchRecord;
		}

		$record->setAttributes($model->getAttributes());

		if($record->save()) {
			//for new records, update the id attr
			$model->setAttribute('id', $record->getAttribute('id'));

			return true;
		} else {
			$model->addErrors($record->getErrors());

			return false;
		}

	}

	/**
	 * Find a search by id
	 * 
	 * @param  $id integer
	 * @return BaseModel|null
	 */
	public function getById($id = 0)
	{
		$record = RetsRabbit_SearchRecord::model()->findById($id);

		if($record) {
			return RetsRabbit_SearchModel::populateModel($record);
		}

		return null;
	}

	/**
	 * Delete a search by id
	 * 
	 * @param  $id integer
	 * @return mixed
	 */
	public function deleteById($id = 0)
	{
		return RetsRabbit_SearchRecord::model()->deleteById($id);
	}
}