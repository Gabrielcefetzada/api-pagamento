<?php

namespace Tests\Integration;

use App\Exceptions\NoBalanceException;
use App\Exceptions\PayeeNotFoundException;
use App\Exceptions\TransferActingAsAnotherUserException;
use App\Exceptions\TransferToYourSelfException;
use App\Http\Requests\Transfer;
use App\Http\Services\TransferenceService;
use App\Interfaces\AntiFraudInterface;
use App\Jobs\SendTransferenceDoneNotification;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransferenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $payer;
    protected User $payee;
    protected Wallet $payerWallet;
    protected Wallet $payeeWallet;
    protected AntiFraudInterface $antiFraud;
    protected TransferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $this->createUsers();
        $this->createWallets();

        $this->antiFraud = \Mockery::mock(AntiFraudInterface::class);
        $this->antiFraud->shouldReceive('authorize')->andReturn(true);

        Notification::fake();

        $this->service = new TransferenceService($this->payerWallet, $this->antiFraud);
    }

    private function createUsers(): void
    {
        $this->payer = User::factory()->make();
        $this->payer->saveQuietly();

        $this->payee = User::factory()->make();
        $this->payee->saveQuietly();

        $this->actingAs($this->payer);
    }

    private function createWallets(): void
    {
        $this->payerWallet = Wallet::factory()->create([
            'user_id' => $this->payer->id,
            'balance' => 10000,
        ]);

        $this->payeeWallet = Wallet::factory()->create([
            'user_id' => $this->payee->id,
            'balance' => 5000,
        ]);
    }

    public function test_successful_transfer()
    {
        $request = new Transfer([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => 50,
        ]);

        $this->service->transfer($request);

        $this->assertDatabaseHas('transferences', [
            'payer_id' => $this->payer->id,
            'payee_id' => $this->payee->id,
            'amount'   => 5000,
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $this->payer->id,
            'balance' => 5000,
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $this->payee->id,
            'balance' => 10000,
        ]);

        Queue::assertPushed(SendTransferenceDoneNotification::class);
    }

    public function test_transfer_to_self()
    {
        $request = new Transfer([
            'payer' => $this->payer->id,
            'payee' => $this->payer->id,
            'value' => 50,
        ]);

        $this->expectException(TransferToYourSelfException::class);
        $this->service->transfer($request);
    }

    public function test_insufficient_funds()
    {
        $request = new Transfer([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => 200,
        ]);

        $this->expectException(NoBalanceException::class);
        $this->service->transfer($request);
    }

    public function test_payee_not_found()
    {
        $request = new Transfer([
            'payer' => $this->payer->id,
            'payee' => 9999,
            'value' => 50,
        ]);

        $this->expectException(PayeeNotFoundException::class);
        $this->service->transfer($request);
    }

    public function test_transfer_as_another_user()
    {
        $anotherUser = User::factory()->create();
        $this->actingAs($anotherUser);

        $request = new Transfer([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => 50,
        ]);

        $this->expectException(TransferActingAsAnotherUserException::class);
        $this->service->transfer($request);
    }
}
