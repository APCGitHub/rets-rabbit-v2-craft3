<?php /** @noinspection ALL */

namespace apc\retsrabbit\services;

use Apc\RetsRabbit\Core\Query\QueryParser;
use Craft;

use craft\base\Component;

class FormsService extends Component
{
	/**
	 * Convert form params to RESO standard format
	 * 
	 * @param  $params array
	 * @return array
	 */
	public function toReso($params = [])
	{
		$reso = (new QueryParser)->format($params);

		return array_filter($reso, function ($value) {
            return !empty($value);
        });
	}
}