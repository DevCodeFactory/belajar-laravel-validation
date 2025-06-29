<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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

    public function testValidationException()
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

        try {
            $validator->validate();
            self::fail('ValidationException was not thrown');
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
//            $message = $exception->errors(); // OR
//            $message = $exception->validator->getMessageBag(); // OR
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

}
