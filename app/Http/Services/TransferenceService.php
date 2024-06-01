<?php

namespace App\Http\Services;

use App\Http\Requests\Transfer;
use App\Models\Transference;
use App\Models\Wallet;

class TransferenceService
{
    public function __construct(
        public Wallet $walletModel,
    ) {
    }
    public function transfer(Transfer $request)
    {
        if ($request['payer'] != auth()->id()) {
            throw new \Exception('Você não pode fazer uma transferência como outro usuário');
        }

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

        $payeeWallet->update(['balance' => $payeeWallet['balance'] + $request['value']]);
        $payerWallet->update(['balance' => $payerWallet['balance'] - $request['value']]);

        return [
            'success'         => true,
            'transference_id' => $transference->id,
            'amount'          => $transference->amount * 100,
        ];
    }

    private function checkPayerBalance(Wallet $payerWallet, int $transferenceAmount)
    {
        if ($payerWallet->balance < $transferenceAmount) {
            throw new \Exception("Saldo insuficiente. Você tentou realizar uma transferência de R$ " . $transferenceAmount / 100 . ", mas possui na carteira R$ " . $payerWallet->getHumanBalance());
        }
    }
}
