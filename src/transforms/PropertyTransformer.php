<?php

namespace apc\retsrabbit\transformers;

use League\Fractal\TransformerAbstract;

class PropertyTransformer extends TransformerAbstract
{
    /**
     * @param  $listing array
     * @return array|null
     */
    public function transform($listing = array())
    {
        $data = $listing;
        $data['hasPhotos'] = false;
        $data['totalPhotos'] = 0;

        if(isset($data['listing']) && isset($data['listing']['photos'])) {
            $count = sizeof($data['listing']['photos']);
            if($count) {
                $data['hasPhotos'] = true;
                $data['totalPhotos'] = $count;
            }
        }

        return $data;
    }
}