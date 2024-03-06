<?php

namespace App\Http\Requests;

use App\Models\Message;


class StoreMessageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required|string|max:800',
            'scheduled_opening_time' => 'required',
        ];
    }
}
