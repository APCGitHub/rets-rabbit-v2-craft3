<?php


namespace apc\retsrabbit\traits;


use apc\retsrabbit\services\CacheService;
use apc\retsrabbit\services\FormsService;
use apc\retsrabbit\services\PropertiesService;
use apc\retsrabbit\services\SearchesService;
use apc\retsrabbit\services\TokensService;

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