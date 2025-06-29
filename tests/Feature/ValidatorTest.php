<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Illuminate\Support\Facades\App;
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

    public function testValidatorMultipleRules()
    {
        App::setLocale('id');

        $data = [
            'username' => 'fahmi',
            'password' => 'fahmi',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
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

    public function testValidatorValidData()
    {
        $data = [
            'username' => 'fahmi@gmail.com',
            'password' => 'rahasia',
            'admin' => true,
            'other' => 'xxx'
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $validData = $validator->validate();
            Log::info(json_encode($validData, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidatorInlineMessage()
    {
        $data = [
            'username' => 'fahmi',
            'password' => 'fahmi',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $messages = [
            'required' => ':attribute tidak boleh kosong.',
            'email' => ':attribute harus berupa email.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
        ];

        $validator = Validator::make($data, $rules, $messages);
        self::assertNotNull($validator);
        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        self::assertIsArray($message->toArray());
        self::assertJson($message->toJson());
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testAdditionalValidation()
    {
        App::setLocale('id');

        $data = [
            'username' => 'fahmi@gmail.com',
            'password' => 'fahmi@gmail.com',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $data = $validator->getData();
            if($data['username'] == $data['password']) {
                $validator->errors()->add('password', 'Password tidak boleh sama dengan username.');
            }
        });
        self::assertNotNull($validator);
        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        self::assertJson($message->toJson());
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorCustomRule()
    {
//        App::setLocale('id');

        $data = [
            'username' => 'fahmi@gmail.com',
            'password' => 'fahmi@gmail.com',
        ];

        $rules = [
            'username' => ['required', 'email', 'max:100', new Uppercase()],
            'password' => ['required', 'min:6', 'max:20', new RegistrationRule()],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        self::assertJson($message->toJson());
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

}
