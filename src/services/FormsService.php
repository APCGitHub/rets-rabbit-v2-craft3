<?php /** @noinspection ALL */

namespace anecka\retsrabbit\services;

use Anecka\RetsRabbit\Core\Query\QueryParser;
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
	public function toReso($params = array())
	{
		$reso = (new QueryParser)->format($params);
		$reso = array_filter($reso, function ($value) {
			return !empty($value);
		});

		return $reso;
	}
}