<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidator()
    {
        $data = [
            'username' => 'admin',
            'password' => 'rahasia',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        self::assertNotNull($validator);
        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }

    public function testValidatorInvalid()
    {
        $data = [
            'username' => '',
            'password' => '',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        self::assertNotNull($validator);
        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        self::assertIsArray($message->toArray());
        self::assertJson($message->toJson());
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

}
