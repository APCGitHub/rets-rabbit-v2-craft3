<?php


namespace anecka\retsrabbit\Traits;


use anecka\retsrabbit\services\CacheService;
use anecka\retsrabbit\services\FormsService;
use anecka\retsrabbit\services\PropertiesService;
use anecka\retsrabbit\services\SearchesService;
use anecka\retsrabbit\services\TokensService;

trait PluginTrait
{
    /**
     * @return CacheService
     */
    public function getCache(): CacheService
    {
        return $this->get('cache');
    }

    /**
     * @return FormsService
     */
    public function getForms(): FormsService
    {
        return $this->get('forms');
    }

    /**
     * @return PropertiesService
     */
    public function getProperties(): PropertiesService
    {
        return $this->get('properties');
    }

    /**
     * @return SearchesService
     */
    public function getSearches(): SearchesService
    {
        return $this->get('searches');
    }

    /**
     * @return TokensService
     */
    public function getTokens(): TokensService
    {
        return $this->get('tokens');
    }
}