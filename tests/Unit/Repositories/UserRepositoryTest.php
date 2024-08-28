<?php

namespace Tests\Unit\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Repositories\UserRepository;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    private static function repository(): UserRepository|RepositoryInterface
    {
        return new UserRepository;
    }

    public function test_find_user_by_id()
    {
        $model = self::repository()->factory()->create();
        $foundModel = self::repository()->find($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }
}
