<?php

namespace Tests\Unit\Services;

use App\Http\Requests\TransferRequest;
use App\Models\Role;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\AuthorizationService;
use App\Services\NotificationService;
use App\Services\TransferService;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    protected $userRepository;
    protected $walletRepository;
    protected $transactionRepository;
    protected $authorizationService;
    protected $notificationService;
    protected $transferService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new UserRepository();
        $this->walletRepository = new WalletRepository();
        $this->transactionRepository = new TransactionRepository();
        $this->authorizationService = new AuthorizationService();
        $this->notificationService = new NotificationService();

        $this->transferService = new TransferService(
            $this->userRepository,
            $this->walletRepository,
            $this->transactionRepository,
            $this->authorizationService,
            $this->notificationService
        );
    }

    public function test_transfer_success()
    {
        $payer = UserRepository::factory()->create(['role_id' => Role::USER]);
        $payer->wallet->update(['balance' => 100]);

        $payee = UserRepository::factory()->create(['role_id' => Role::RETAILER]);

        $request = new TransferRequest();

        $request->setUserResolver(function () use ($payer) {
            return $payer;
        });

        $request->merge(['payee' => $payee->id]);
        $request->merge(['value' => 50.00]);

        $result = $this->transferService->handleTransfer($request);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals('Transfer completed successfully.', $result['message']);
    }

    public function test_transfer_insufficient_balance()
    {
        $payer = UserRepository::factory()->create(['role_id' => Role::USER]);
        $payer->wallet->update(['balance' => 25]);

        $payee = UserRepository::factory()->create(['role_id' => Role::RETAILER]);

        $request = new TransferRequest();

        $request->setUserResolver(function () use ($payer) {
            return $payer;
        });

        $request->merge(['payee' => $payee->id]);
        $request->merge(['value' => 50.00]);

        $result = $this->transferService->handleTransfer($request);

        $this->assertEquals('error', $result['status']);
        $this->assertEquals('Insufficient balance.', $result['message']);
        $this->assertEquals(403, $result['code']);
    }

    public function test_transfer_unauthorized_user()
    {
        $payer = UserRepository::factory()->create(['role_id' => Role::RETAILER]);
        $payer->wallet->update(['balance' => 100]);

        $payee = UserRepository::factory()->create(['role_id' => Role::RETAILER]);

        $request = new TransferRequest();

        $request->setUserResolver(function () use ($payer) {
            return $payer;
        });

        $request->merge(['payee' => $payee->id]);
        $request->merge(['value' => 50.00]);

        $result = $this->transferService->handleTransfer($request);

        $this->assertEquals('error', $result['status']);
        $this->assertEquals('Retailers cannot make transfers.', $result['message']);
        $this->assertEquals(403, $result['code']);
    }
}
