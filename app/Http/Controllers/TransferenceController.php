<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transfer;
use App\Http\Services\TransferenceService;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class TransferenceController extends Controller
{
    public function transfer(Transfer $request)
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
