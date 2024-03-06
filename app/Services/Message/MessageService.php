<?php

namespace App\Services\Message;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Traits\Filterable;
use App\Utilities\Data;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class MessageService
{

    /**
     * Get a single resource from the database
     *
     * @param  Message  $message
     *
     * @return MessageResource
     */
    public function get(Message $message)
    {
        return $message;
    }

    /**
     * Get resource index from the database
     *
     * @param $query
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {

        $query = Message::query();
        $query->where('user_id', auth()->id());

        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return MessageResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     *
     * @param  array  $data
     *
     * @return Builder|Model|null
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function create(array $data)
    {
        $data = $this->clean($data);
        $data['user_id'] = auth()->id();
        $record = Message::query()->create($data);
        if (!empty($record)) {
            return $record->fresh();
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     *
     * @param  Message  $message
     * @param  array  $data
     *
     * @return bool
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Message $message, array $data)
    {

        $data = $this->clean($data);

        return $message->update($data);
    }

    /**
     * Deletes resource in the database
     *
     * @param  Message|Model  $message
     *
     * @return bool
     */
    public function delete(Message $message)
    {
        return $message->delete();
    }

    /**
     * Clean the data
     *
     * @param  array  $data
     *
     * @return array
     */
    private function clean(array $data)
    {
        foreach ($data as $i => $row) {
            if ('null' === $row) {
                $data[$i] = null;
            }
        }

        return $data;
    }

    /**
     * Filter resources
     * @return void
     */
    private function filter(Builder &$query, $filters)
    {
        $query->filter(Arr::except($filters, ['un_opened']));

        if (!empty($filters['un_opened'])) {
            $unOpenedFilter = Filterable::parseFilter($filters['un_opened']);

            if($unOpenedFilter) {
                $query->where('is_opened', false)->where('scheduled_opening_time', '<=', now());
            }
        }

    }
}
