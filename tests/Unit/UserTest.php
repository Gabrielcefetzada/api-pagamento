<?php

namespace Tests;

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;

class UserTest extends TestCase
{
    public function test_register_with_invalid_cpf()
    {a
        $response = $this->postJson('/register-common-user', [
            'name'     => 'John Doe',
            'cpf'      => '12345678900',
            'email'    => 'john@example2.com',
            'password' => 'Mpassword123!',
        ]);

        $response->assertStatus(422);
    }
}
