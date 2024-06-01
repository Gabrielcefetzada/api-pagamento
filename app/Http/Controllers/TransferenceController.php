<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transfer;
use App\Http\Resources\TranseferenceResource;
use App\Http\Services\TransferenceService;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransferenceController extends Controller
{
    public function store(Transfer $request): TranseferenceResource|JsonResponse
    {
        try {
            DB::beginTransaction();
            $response = (new TransferenceService(new Wallet()))->transfer($request);
            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
