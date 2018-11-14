<?php

namespace apc\retsrabbit\services;

use Craft;

use craft\base\Component;

/**
 *
 */
class CacheService extends Component
{
	/**
	 * The base for all rets rabbit cache items
	 * 
	 * @var string
	 */
	private $basePath = '/rets-rabbit/';

    /**
     * @param string
     * @param mixed
     * @param int
     * @param boolean
     * @return bool
     */
	public function set($id, $value, $expire = 3600, $secure = false): bool
	{
		$key = $this->basePath . $id;

		if($secure) {
			$value = Craft::$app->security->encrypt($value);
		}

		return Craft::$app->cache->set($key, $value, $expire);
	}

    /**
     * @param  string
     * @param bool $secure
     * @return mixed|null
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
	public function get($id, $secure = false)
	{
		$key = $this->basePath . $id;
		$value = Craft::$app->cache->get($key);

		if($value && $secure) {
			$value = Craft::$app->security->decrypt($value);
		}

		return $value;
	}

	/**
	 * @param  string
	 * @return boolean
	 */
	public function delete($id): bool
    {
		$key = $this->basePath . $id;

		return Craft::$app->cache->delete($key);		
	}
}