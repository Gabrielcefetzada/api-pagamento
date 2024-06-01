<?php

namespace App\Http\Services;

use App\Http\Requests\Transfer;
use App\Http\Resources\TranseferenceResource;
use App\Interfaces\AntiFraudInterface;
use App\Models\Transference;
use App\Models\Wallet;

class TransferenceService
{
    public function __construct(
        public Wallet             $walletModel,
        public AntiFraudInterface $antiFraud
    ) {
    }

    public function transfer(Transfer $request): TranseferenceResource
    {
        $this->firstChecks($request);

        $payeeWallet = $this->walletModel::with('user')->where('user_id', $request['payee'])->sharedLock()->first();
        $payerWallet = $this->walletModel::with('user')->where('user_id', $request['payer'])->sharedLock()->first();

        if (!$payeeWallet) {
            throw new \Exception('Destinatário não cadastrado!');
        }

        $request['value'] = $request['value'] * 100;

        $this->checkPayerBalance($payerWallet, $request['value']);

        $transference = Transference::create(
            [
                'payer_id' => $request['payer'],
                'payee_id' => $request['payee'],
                'amount'   => $request['value'],
            ]
        );

        $transferenceWithAgents = ($transference::with(['payee'])->where('id', $transference->id)->first());

        $payeeWallet->update(['balance' => $payeeWallet['balance'] + $request['value']]);
        $payerWallet->update(['balance' => $payerWallet['balance'] - $request['value']]);

        return new TranseferenceResource($transferenceWithAgents);
    }

    private function firstChecks(Transfer $request)
    {
        if ($request['payer'] != auth()->id()) {
            throw new \Exception('Você deve fazer a transferência com seu usuário');
        }

        if ($request['payer'] === $request['payee']) {
            throw new \Exception('Não é possível realizar transferências para si mesmo');
        }

        if (!$this->antiFraud->authorize()) {
            throw new \Exception('Não autorizado');
        }
    }

    private function checkPayerBalance(Wallet $payerWallet, int $transferenceAmount)
    {
        if ($payerWallet->balance < $transferenceAmount) {
            throw new \Exception("Saldo insuficiente. Você tentou realizar uma transferência de R$ " . $transferenceAmount / 100 . ", mas possui na carteira R$ " . $payerWallet->getHumanBalance());
        }
    }
}
