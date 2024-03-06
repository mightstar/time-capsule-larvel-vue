<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\Filterable;
use App\Utilities\Data;
use Bouncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class UserService
{

    /**
     * Get a single resource from the database
     *
     * @param  User  $user
     *
     * @return UserResource
     */
    public function get(User $user)
    {
        return new UserResource($user);
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
        $query = User::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return UserResource::collection($query->paginate(10));
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

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $data['email_verified_at'] = Carbon::now()->toDateTimeString();

        $record = User::query()->create($data);
        if (!empty($record)) {
            return $record->fresh();
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     *
     * @param  User  $user
     * @param  array  $data
     *
     * @return bool
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(User $user, array $data)
    {
        $data = $this->clean($data);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        unset($data['email']);

        return $user->update($data);
    }

    /**
     * Deletes resource in the database
     *
     * @param  User|Model  $user
     *
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->delete();
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
        $query->filter(Arr::except($filters));
    }
}
