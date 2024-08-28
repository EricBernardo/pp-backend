<?php

namespace Tests\Unit\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Role;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    private static function repository(): TransactionRepository|RepositoryInterface
    {
        return new TransactionRepository;
    }

    private static function repositoryUser(): UserRepository|RepositoryInterface
    {
        return new UserRepository;
    }

    public function test_create_transaction()
    {
        $payer = self::repositoryUser()->factory()->create([
            'role_id' => Role::USER
        ]);

        $payee = self::repositoryUser()->factory()->create([
            'role_id' => Role::RETAILER
        ]);

        $transaction = self::repository()->create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 100.00
        ]);

        $this->assertInstanceOf(Transaction::class, $transaction);

        $this->assertEquals($payer->id, $transaction->payer_id);
        $this->assertEquals($payee->id, $transaction->payee_id);
    }
}
