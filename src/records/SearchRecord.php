<?php

namespace anecka\retsrabbit\records;

use craft\db\ActiveRecord;

class SearchRecord extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%rets_rabbit_searches}}';
    }
}