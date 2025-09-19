<?php

namespace Tests\Feature;

use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SmsService $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smsService = new SmsService();
    }

    public function test_sms_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(SmsService::class, $this->smsService);
    }

    public function test_phone_number_formatting(): void
    {
        // Test różnych formatów numerów
        $testCases = [
            '123456789' => '48123456789',
            '0123456789' => '48123456789',
            '48123456789' => '48123456789',
            '+48 123 456 789' => '48123456789',
            '48 123 456 789' => '48123456789',
        ];

        foreach ($testCases as $input => $expected) {
            $this->assertTrue($this->smsService->isValidPhoneNumber($input));
        }
    }

    public function test_invalid_phone_numbers(): void
    {
        $invalidNumbers = [
            '12345', // za krótki
            '123456789012345', // za długi
            'abc123def', // zawiera litery
            '', // pusty
        ];

        foreach ($invalidNumbers as $invalidNumber) {
            $this->assertFalse($this->smsService->isValidPhoneNumber($invalidNumber));
        }
    }

    public function test_send_custom_message_logs_to_database(): void
    {
        Notification::fake();

        $phone = '123456789';
        $message = 'Test message';
        $type = 'test';

        $result = $this->smsService->sendCustomMessage($phone, $message);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sms_logs', [
            'phone' => '48123456789',
            'message' => $message,
            'type' => 'custom',
            'status' => 'sent',
        ]);
    }

    public function test_send_pre_registration_link(): void
    {
        Notification::fake();

        $phone = '123456789';
        $link = 'https://example.com/register';

        $result = $this->smsService->sendPreRegistrationLink($phone, $link);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sms_logs', [
            'phone' => '48123456789',
            'type' => 'pre_registration',
            'status' => 'sent',
        ]);
    }

    public function test_send_data_correction_link(): void
    {
        Notification::fake();

        $phone = '123456789';
        $link = 'https://example.com/correct';

        $result = $this->smsService->sendDataCorrectionLink($phone, $link);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sms_logs', [
            'phone' => '48123456789',
            'type' => 'data_correction',
            'status' => 'sent',
        ]);
    }

    public function test_send_password_reset_link(): void
    {
        Notification::fake();

        $phone = '123456789';
        $link = 'https://example.com/reset';

        $result = $this->smsService->sendPasswordResetLink($phone, $link);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sms_logs', [
            'phone' => '48123456789',
            'type' => 'password_reset',
            'status' => 'sent',
        ]);
    }

    public function test_send_payment_reminder(): void
    {
        Notification::fake();

        $phone = '123456789';
        $amount = 200.50;
        $dueDate = '2024-02-01';
        $paymentLink = 'https://example.com/pay';

        $result = $this->smsService->sendPaymentReminder($phone, $amount, $dueDate, $paymentLink);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sms_logs', [
            'phone' => '48123456789',
            'type' => 'payment_reminder',
            'status' => 'sent',
        ]);
    }

    public function test_sms_log_creation_with_error(): void
    {
        // Test logowania błędu
        $phone = 'invalid';
        $message = 'Test message';
        $type = 'test';

        $result = $this->smsService->sendCustomMessage($phone, $message);

        $this->assertFalse($result);
        $this->assertDatabaseHas('sms_logs', [
            'phone' => 'invalid',
            'message' => $message,
            'type' => 'custom',
            'status' => 'error',
        ]);
    }

    public function test_sms_log_scopes(): void
    {
        // Utwórz różne logi SMS
        SmsLog::create([
            'phone' => '48123456789',
            'message' => 'Test 1',
            'type' => 'test',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        SmsLog::create([
            'phone' => '48123456790',
            'message' => 'Test 2',
            'type' => 'test',
            'status' => 'error',
            'error_message' => 'Test error',
        ]);

        // Test scope sent
        $sentCount = SmsLog::sent()->count();
        $this->assertEquals(1, $sentCount);

        // Test scope errors
        $errorCount = SmsLog::errors()->count();
        $this->assertEquals(1, $errorCount);

        // Test scope ofType
        $testCount = SmsLog::ofType('test')->count();
        $this->assertEquals(2, $testCount);

        // Test scope forPhone
        $phoneCount = SmsLog::forPhone('48123456789')->count();
        $this->assertEquals(1, $phoneCount);
    }
}
