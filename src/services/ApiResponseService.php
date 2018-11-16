<?php


namespace apc\retsrabbit\services;

use Apc\RetsRabbit\Core\ApiResponse;

use craft\base\Component;

class ApiResponseService extends Component
{
    /**
     * @var string
     */
    private $permission_code = 'permission';

    /**
     * @param ApiResponse $response
     * @return bool
     */
    public function hasPermissionError(ApiResponse $response): bool
    {
        $has_error = false;
        $contents = $response->getResponse();

        if(isset($contents['error']['code'])) {
            $code = $contents['error']['code'];

            if($code === $this->permission_code) {
                $has_error = true;
            }
        }

        return $has_error;
    }

    /**
     * @param ApiResponse $response
     * @return string
     */
    public function getResponseErrors(ApiResponse $response): string
    {
        $contents = $response->getResponse();

        return $contents['error']['message'] ?? '';
    }
}