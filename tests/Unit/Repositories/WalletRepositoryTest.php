<?php

namespace Tests\Unit\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Tests\TestCase;

class WalletRepositoryTest extends TestCase
{
    private static function repository(): WalletRepository|RepositoryInterface
    {
        return new WalletRepository;
    }

    private static function repositoryUser(): UserRepository|RepositoryInterface
    {
        return new UserRepository;
    }

    public function test_update_wallet_balance()
    {
        $balance = 50.00;

        $user = self::repositoryUser()->factory()->create();

        $updated = self::repository()->update($user->wallet->id, [
            'balance' => $balance
        ]);

        $this->assertEquals(1, $updated);

        $walletUpdated = self::repository()->find($user->wallet->id);

        $this->assertEquals($balance, $walletUpdated->balance);
    }
}
