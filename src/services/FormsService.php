<?php

namespace anecka\retsrabbit\services;

use Craft;
use craft\base\Component;
use RetsRabbit\Query\QueryParser;

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

		RetsRabbitPlugin::log(print_r($reso, true));

		return $reso;
	}
}