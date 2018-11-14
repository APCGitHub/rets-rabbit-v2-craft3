<?php

namespace anecka\retsrabbit\services;

use Craft;

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
    public function newSearch($attributes = []): Search
    {
        $model = new Search();

        if (isset($attributes['params'])) {
            $attributes['params'] = json_encode($attributes['params']);
        }

        foreach($attributes as $k => $v) {
            $model->$k = $v;
        }

        $model->params = $attributes['params'];
        $model->siteId = Craft::$app->sites->currentSite->id;

        return $model;
    }

    /**
     * Create a new search model with a 'property' type
     *
     * @param  array
     * @return Search
     */
    public function newPropertySearch($attributes = []): Search
    {
        $attributes['type'] = 'property';

        return $this->newSearch($attributes);
    }

    /**
     * Save a search
     *
     * @param Search $model
     * @param bool $runValidation
     * @return bool
     * @throws InvalidSearchException
     */
    public function saveSearch(Search $model, bool $runValidation = true): bool
    {
        if ($runValidation && !$model->validate()) {
            Craft::info('Search not saved due to validation error.', __METHOD__);

            return false;
        }

        if ($model->id) {
            $record = SearchRecord::findOne($model->id);

            if (!$record) {
                throw new InvalidSearchException("No search exists with the ID '{$model->id}'");
            }
        } else {
            $record = new SearchRecord;
        }

        $record->siteId = $model->siteId;
        $record->params = $model->params;
        $record->type   = $model->type;
        $record->save(false);

        if(!$model->id) {
            $model->id = $record->id;
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

        if ($record) {
            return new Search($record->toArray());
        }

        return null;
    }

    /**
     * Delete a search by id
     *
     * @param  $id integer
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteById($id = 0): bool
    {
        $record = SearchRecord::findOne($id);

        if (!$record) {
            return true;
        }

        $record->delete();

        return true;
    }
}