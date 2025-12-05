<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_URL = '/register-common-user';

    public function test_register_common_user_with_missing_required_fields(): void
    {
        $invalidData = [
            'name'     => 'João Silva',
            'password' => 'Senha@123',
        ];

        $response = $this->postJson(self::REGISTER_URL, $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf', 'email']);
    }
}
