<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transfer;
use App\Http\Resources\TranseferenceResource;
use App\Http\Services\AntiFraudService;
use App\Http\Services\TransferenceService;
use App\Models\Wallet;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransferenceController extends Controller
{
    public function transfer(Transfer $request): TranseferenceResource|Response
    {
        try {
            DB::beginTransaction();
            $response = (new TransferenceService(new Wallet(), new AntiFraudService()))->transfer($request);
            DB::commit();
            return $response;

        } catch (\Exception $ex) {
            DB::rollBack();

            if (method_exists($ex, 'render')) {
                return $ex->render($request);
            }

            dump($ex->getMessage());
            Log::error($ex);

            return response([
                'error' => [
                    'httpCode' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    'message'  => 'Erro de servidor',
                ],
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
