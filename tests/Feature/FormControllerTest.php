<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    public function testLoginFailed()
    {
        $this->post('/form/login', [
            'username' => '',
            'password' => '',
        ])->assertStatus(400);
    }

    public function testLoginSuccess()
    {
        $this->post('/form/login', [
            'username' => 'admin',
            'password' => 'rahasia',
        ])->assertStatus(200);
    }

    public function testFormFailed()
    {
        $this->post('/form', [
            'username' => '',
            'password' => '',
        ])->assertStatus(302);
    }

    public function testFormSuccess()
    {
        $this->post('/form', [
            'username' => 'admin',
            'password' => 'rahasia',
        ])->assertStatus(200);
    }
}
