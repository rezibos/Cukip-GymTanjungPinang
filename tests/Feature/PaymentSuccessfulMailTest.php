<?php

namespace Tests\Feature;

use App\Mail\PaymentSuccessfulMail;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSuccessfulMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_renders_verified_payment_details(): void
    {
        $payment = Payment::factory()->create([
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '6281234567890',
            'checkout_items' => [[
                'name' => 'Regular Bulanan',
                'quantity' => 2,
                'subtotal' => 500000,
            ]],
            'gross_amount' => 500000,
            'payment_type' => 'bank_transfer',
        ]);
        $mail = new PaymentSuccessfulMail($payment, 'Sementara-123');

        $mail->assertHasSubject('Pembayaran Berhasil - '.$payment->order_id)
            ->assertSeeInHtml('Pembayaran berhasil')
            ->assertSeeInHtml('Budi Santoso')
            ->assertSeeInHtml('budi@example.com')
            ->assertSeeInHtml('6281234567890')
            ->assertSeeInHtml('Regular Bulanan')
            ->assertSeeInHtml('Rp500.000')
            ->assertSeeInHtml($payment->order_id)
            ->assertSeeInHtml('Sementara-123');
    }

    public function test_email_does_not_expose_password_for_an_existing_account(): void
    {
        $payment = Payment::factory()->create([
            'customer_email' => 'member@example.com',
        ]);
        $mail = new PaymentSuccessfulMail($payment);

        $mail->assertSeeInHtml('member@example.com')
            ->assertDontSeeInHtml('Password sementara');
    }

    public function test_email_escapes_customer_input(): void
    {
        $payment = Payment::factory()->create([
            'customer_name' => '<script>alert("xss")</script>',
            'checkout_items' => [[
                'name' => '<img src=x onerror=alert(1)>',
                'quantity' => 1,
                'subtotal' => 50000,
            ]],
        ]);

        $html = (new PaymentSuccessfulMail($payment))->render();

        $this->assertStringNotContainsString('<script>alert("xss")</script>', $html);
        $this->assertStringNotContainsString('<img src=x onerror=alert(1)>', $html);
    }
}
