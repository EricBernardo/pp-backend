<?php

namespace App\Services;

use App\Http\Requests\TransferRequest;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\AuthorizationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    protected $userRepository;
    protected $walletRepository;
    protected $transactionRepository;
    protected $authorizationService;
    protected $notificationService;

    public function __construct(
        UserRepository $userRepository,
        WalletRepository $walletRepository,
        TransactionRepository $transactionRepository,
        AuthorizationService $authorizationService,
        NotificationService $notificationService
    ) {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;
        $this->authorizationService = $authorizationService;
        $this->notificationService = $notificationService;
    }

    public function handleTransfer(TransferRequest $request): array
    {
        $payer = $request->user();

        if ($payer->isRetailer()) {
            return [
                'status' => 'error',
                'message' => 'Retailers cannot make transfers.',
                'code' => 403
            ];
        }

        $payee = $this->userRepository->find($request->payee);
        $payerWallet = $payer->wallet;

        if ($payerWallet->balance < $request->value) {
            return [
                'status' => 'error',
                'message' => 'Insufficient balance.',
                'code' => 403
            ];
        }

        if (!$this->authorizationService->isAuthorized()) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized transaction.',
                'code' => 403
            ];
        }

        DB::beginTransaction();

        try {
            $this->walletRepository->update($payerWallet->id, [
                'balance' => $payerWallet->balance - $request->value
            ]);

            $payeeWallet = $payee->wallet;
            $this->walletRepository->update($payeeWallet->id, [
                'balance' => $payeeWallet->balance + $request->value
            ]);

            $transaction = $this->transactionRepository->create([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'amount' => $request->value
            ]);

            DB::commit();

            $this->notificationService->send($transaction);

            return [
                'status' => 'success',
                'message' => 'Transfer completed successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error([
                'message' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => 'Error processing transaction.',
                'code' => 500
            ];
        }
    }
}
