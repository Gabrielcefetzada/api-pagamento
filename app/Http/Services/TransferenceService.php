<?php

namespace App\Http\Services;

use App\Exceptions\AntiFraudException;
use App\Exceptions\NoBalanceException;
use App\Exceptions\PayeeNotFoundException;
use App\Exceptions\TransferActingAsAnotherUserException;
use App\Exceptions\TransferToYourSelfException;
use App\Http\Requests\Transfer;
use App\Http\Resources\TranseferenceResource;
use App\Interfaces\AntiFraudInterface;
use App\Jobs\SendTransferenceDoneNotification;
use App\Models\Transference;
use App\Models\Wallet;

class TransferenceService
{
    public function __construct(
        public Wallet $walletModel,
        public AntiFraudInterface $antiFraud,
    ) {
    }

    /**
     * @throws AntiFraudException
     * @throws NoBalanceException
     * @throws TransferActingAsAnotherUserException
     * @throws TransferToYourSelfException
     * @throws PayeeNotFoundException
     */
    public function transfer(Transfer $request): TranseferenceResource
    {
        $this->firstChecks($request);

        $payeeWallet = $this->walletModel::with('user')->where('user_id', $request['payee'])->sharedLock()->first();
        $payerWallet = $this->walletModel::with('user')->where('user_id', $request['payer'])->sharedLock()->first();

        if (! $payeeWallet) {
            throw new PayeeNotFoundException(['payee' => $request['payee']]);
        }

        $request['value'] = $request['value'] * 100;

        $this->checkPayerBalance($payerWallet, $request['value']);

        $transferenceWithAgents = $this->persistTransference($request, $payeeWallet, $payerWallet);

        SendTransferenceDoneNotification::dispatch($transferenceWithAgents);

        return new TranseferenceResource($transferenceWithAgents);
    }

    /**
     * @throws AntiFraudException
     * @throws TransferToYourSelfException
     * @throws TransferActingAsAnotherUserException
     */
    private function firstChecks(Transfer $request): void
    {
        if ($request['payer'] != auth()->id()) {
            throw new TransferActingAsAnotherUserException([]);
        }

        if ($request['payer'] === $request['payee']) {
            throw new TransferToYourSelfException([]);
        }

        if (! $this->antiFraud->authorize()) {
            throw new AntiFraudException([]);
        }
    }

    /**
     * @throws NoBalanceException
     */
    private function checkPayerBalance(Wallet $payerWallet, int $transferenceAmount)
    {
        if ($payerWallet->balance < $transferenceAmount) {
            throw new NoBalanceException([
                'transferenceAmount' => $transferenceAmount / 100,
                'payerWallet'        => $payerWallet->getHumanBalance(),
            ]);
        }
    }

    private function persistTransference(Transfer $request, Wallet $payeeWallet, Wallet $payerWallet): Transference
    {
        $transference = Transference::create(
            [
                'payer_id' => $request['payer'],
                'payee_id' => $request['payee'],
                'amount'   => $request['value'],
            ]
        );

        $transferenceWithAgents = ($transference::with(['payee', 'payer'])->where('id', $transference->id)->first());

        $payeeWallet->update(['balance' => $payeeWallet['balance'] + $request['value']]);
        $payerWallet->update(['balance' => $payerWallet['balance'] - $request['value']]);

        return $transferenceWithAgents;
    }
}
