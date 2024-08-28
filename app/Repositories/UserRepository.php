<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends AbstractRepository
{
    protected static $model = User::class;

    public static function findByEmail(string $email): User|null
    {
        return self::loadModel()::query()->where(['email' => $email])->first();
    }
}
