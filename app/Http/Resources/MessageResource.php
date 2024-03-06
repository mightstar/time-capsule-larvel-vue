<?php

namespace App\Http\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MessageResource
 * @package App\Http\Resources
 */
class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {

        // $data = $this->resource->toArray();

        // return $data;

        return [
            'id' => $this->id,
            'scheduled_opening_time' => $this->scheduled_opening_time,
            'is_opened' => $this->is_opened,
          ];
    }
}
