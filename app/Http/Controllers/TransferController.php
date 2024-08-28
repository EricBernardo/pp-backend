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

/**
     * @OA\Post (
     *     path="/api/transfer",
     *     tags={"Authenticated"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="value",
     *                          type="decimal"
     *                      ),
     *                      @OA\Property(
     *                          property="payee",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "value":50.50,
     *                     "payee":2
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Transfer completed successfully."
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized transaction."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The selected payee is invalid."),
     *              @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                    property="payee",
     *                    type="array",
     *                    @OA\Items(
     *                       anyOf={
     *                          @OA\Schema(type="string", example="The selected payee is invalid.")
     *                       }
     *                    )
     *                 )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Error processing transaction."),
     *          )
     *      )
     * )
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $result = $this->transferService->handleTransfer($request);

        if ($result['status'] === 'error') {
            return response()->json(['message' => $result['message']], $result['code']);
        }

        return response()->json(['message' => 'Transfer completed successfully.']);
    }
}
