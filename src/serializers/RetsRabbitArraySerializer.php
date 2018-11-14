<?php

namespace apc\retsrabbit\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class RetsRabbitArraySerializer extends ArraySerializer
{
	public function collection($resourceKey, array $data)
	{
		return $data;
	}
}