<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UnauthorizedException as ExceptionsUnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionResourceCollection;
use App\Models\Transactions;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{
    private string $responseName = 'Transactions';
    private array $responseMessage = [
        'index' => 'Get list Transactions successfully',
        'show' => 'Get Detail Transactions successfully',
        'store' => 'Add new Transactions successfully',
        'update' => 'Update Transactions successfully',
        'destroy' => 'Delete Transactions successfully',
        'destroy_failed' => 'Delete Transactions failed, the data is not exists',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(TransactionService $service,Request $request): JsonResponse
    {
        if(auth()->user()->role == 1){
        return $this->responseWithResourceCollection(
            new TransactionResourceCollection($service->orderBy($request)),
            $this->responseName,
            $this->responseMessage['index'],
            JsonResponse::HTTP_OK
        );
    } else {
        return $this->responseWithResourceCollection(
            new TransactionResourceCollection($service->orderByUser($request)),
            $this->responseName,
            $this->responseMessage['index'],
            JsonResponse::HTTP_OK
        );
    }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionService $service, TransactionRequest $request,string $id): JsonResponse
    {

        $stored = $service->addTransaction($id, $request->validated());

        return $this->responseWithResource(
            new TransactionResource($stored),
            $this->responseName,
            $this->responseMessage["store"],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionService $service, string $id): ?JsonResponse
    {

        if(auth()->user()->role == 1 ){
            return $this->responseWithResource(
                new TransactionResource($service->getDataById($id)),
                $this->responseName,
                $this->responseMessage["show"],
                JsonResponse::HTTP_OK
            );
        }else {
            return $this->responseWithResource(
                new TransactionResource($service-> getTransactionUserById($id)),
                $this->responseName,
                $this->responseMessage["show"],
                JsonResponse::HTTP_OK
            );
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionService $service, TransactionRequest $request, string $id): JsonResponse
    {
        $updated = $service->updateDataById($id, $request->validated());

        return $this->responseWithResource(
            new TransactionResource($updated),
            $this->responseName,
            $this->responseMessage["update"],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionService $service, string $id): JsonResponse
    {
        $service->deleteDataById($id);
        return $this->apiResponse([
            "success" => true,
            "name" => $this->responseName,
            "message" => $this->responseMessage["destroy"]
        ], JsonResponse::HTTP_OK);
    }
}
