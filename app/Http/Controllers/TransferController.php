<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    private $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        $result = $this->transferService->handleTransfer($request);

        if ($result['status'] === 'error') {
            return response()->json(['error' => $result['message']], $result['code']);
        }

        return response()->json(['message' => 'Transfer completed successfully.']);
    }
}
