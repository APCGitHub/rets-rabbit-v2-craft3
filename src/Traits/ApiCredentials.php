<?php


namespace apc\retsrabbit\Traits;


use apc\retsrabbit\exceptions\MissingClientCredentials;
use apc\retsrabbit\RetsRabbit;

trait ApiCredentials
{
    /**
     * @throws MissingClientCredentials
     */
    protected function checkCredentials()
    {
        $settings = RetsRabbit::$plugin->getSettings();

        if(empty($settings->clientId) || empty($settings->clientSecret)) {
            throw new MissingClientCredentials();
        }
    }
}