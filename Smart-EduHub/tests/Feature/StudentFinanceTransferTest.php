<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentCard;
use App\Models\StudentCardTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StudentFinanceTransferTest extends TestCase
{
    use RefreshDatabase;

    private Student     $sender;
    private Student     $receiver;
    private StudentCard $sourceCard;
    private StudentCard $targetCard;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('academic_year', '2025-2026');

        $this->sender   = Student::factory()->withPin('111111')->create(['status' => 0]);
        $this->receiver = Student::factory()->create(['status' => 0]);

        $this->sourceCard = StudentCard::create([
            'student_id'  => $this->sender->id,
            'card_number' => 'CARD-SRC-001',
            'balance'     => 100000,
            'status'      => 'active',
            'pin'         => '0000',
        ]);

        $this->targetCard = StudentCard::create([
            'student_id'  => $this->receiver->id,
            'card_number' => 'CARD-TGT-002',
            'balance'     => 50000,
            'status'      => 'active',
            'pin'         => '0000',
        ]);
    }

    // ---------------------------------------------------------------
    // Helper: lakukan langkah confirm → pin → process
    // ---------------------------------------------------------------
    private function doTransfer(int $amount, string $pin): \Illuminate\Testing\TestResponse
    {
        $this->actingAs($this->sender, 'student');

        $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'     => $this->sourceCard->id,
            'target_card_number' => 'CARD-TGT-002',
            'amount'             => $amount,
            'description'        => 'Test transfer',
        ]);

        return $this->post(route('siswa.finance.transfer.process'), [
            'pin' => $pin,
        ]);
    }

    // ---------------------------------------------------------------
    // Test 1: Transfer berhasil dengan PIN benar dan saldo cukup
    // ---------------------------------------------------------------
    public function test_transfer_succeeds_with_correct_pin_and_sufficient_balance(): void
    {
        $this->doTransfer(20000, '111111');

        $this->assertSame(80000.0, (float) $this->sourceCard->fresh()->balance);
        $this->assertSame(70000.0, (float) $this->targetCard->fresh()->balance);
    }

    public function test_transfer_creates_two_transaction_records(): void
    {
        $this->doTransfer(20000, '111111');

        $this->assertSame(2, StudentCardTransaction::count());
    }

    public function test_transfer_creates_debit_and_credit_records(): void
    {
        $this->doTransfer(20000, '111111');

        $this->assertDatabaseHas('student_card_transactions', [
            'card_id' => $this->sourceCard->id,
            'type'    => 'transfer',
            'amount'  => 20000,
        ]);

        $this->assertDatabaseHas('student_card_transactions', [
            'card_id' => $this->targetCard->id,
            'type'    => 'transfer_receive',
            'amount'  => 20000,
        ]);
    }

    public function test_both_transactions_share_same_reference_number(): void
    {
        $this->doTransfer(20000, '111111');

        $txs = StudentCardTransaction::all();
        $this->assertSame(2, $txs->count());
        $this->assertSame(
            $txs->first()->reference_number,
            $txs->last()->reference_number
        );
    }

    // ---------------------------------------------------------------
    // Test 2: Transfer gagal jika PIN salah
    // ---------------------------------------------------------------
    public function test_transfer_fails_with_wrong_pin(): void
    {
        $this->doTransfer(10000, '999999');

        // Saldo tidak berubah
        $this->assertSame(100000.0, (float) $this->sourceCard->fresh()->balance);
        $this->assertSame(50000.0,  (float) $this->targetCard->fresh()->balance);
        $this->assertSame(0, StudentCardTransaction::count());
    }

    // ---------------------------------------------------------------
    // Test 3: Transfer gagal jika saldo tidak cukup
    // ---------------------------------------------------------------
    public function test_transfer_fails_when_insufficient_balance(): void
    {
        $this->actingAs($this->sender, 'student');

        $response = $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'     => $this->sourceCard->id,
            'target_card_number' => 'CARD-TGT-002',
            'amount'             => 999999, // lebih dari saldo
            'description'        => '',
        ]);

        // Controller return back()->with('error') — bukan session errors, tapi session flash
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(0, StudentCardTransaction::count());
    }

    // ---------------------------------------------------------------
    // Test 4: Transfer ke kartu yang sama diblokir
    // ---------------------------------------------------------------
    public function test_transfer_to_same_card_is_blocked(): void
    {
        $this->actingAs($this->sender, 'student');

        $response = $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'     => $this->sourceCard->id,
            'target_card_number' => 'CARD-SRC-001', // kartu yang sama
            'amount'             => 10000,
            'description'        => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(0, StudentCardTransaction::count());
    }

    // ---------------------------------------------------------------
    // Test 5: Transfer ke nomor kartu yang tidak ada diblokir
    // ---------------------------------------------------------------
    public function test_transfer_to_nonexistent_card_is_blocked(): void
    {
        $this->actingAs($this->sender, 'student');

        $response = $this->post(route('siswa.finance.transfer.confirm'), [
            'source_card_id'     => $this->sourceCard->id,
            'target_card_number' => 'CARD-TIDAK-ADA',
            'amount'             => 10000,
            'description'        => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ---------------------------------------------------------------
    // Test 6: Akses detail transaksi milik orang lain → 403
    // ---------------------------------------------------------------
    public function test_student_cannot_view_other_students_transaction(): void
    {
        // Buat transaksi milik sender
        $tx = StudentCardTransaction::create([
            'card_id'          => $this->sourceCard->id,
            'type'             => 'topup',
            'amount'           => 50000,
            'balance_before'   => 50000,
            'balance_after'    => 100000,
            'reference_number' => 'TRF-TEST-001',
            'description'      => 'Test topup',
        ]);

        // Receiver mencoba akses transaksi milik sender
        $this->actingAs($this->receiver, 'student')
            ->get(route('siswa.finance.transactions.show', $tx))
            ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Test 7: Pemilik bisa akses transaksinya sendiri
    // ---------------------------------------------------------------
    public function test_student_can_view_own_transaction(): void
    {
        $tx = StudentCardTransaction::create([
            'card_id'          => $this->sourceCard->id,
            'type'             => 'topup',
            'amount'           => 50000,
            'balance_before'   => 50000,
            'balance_after'    => 100000,
            'reference_number' => 'TRF-TEST-002',
            'description'      => 'Test topup',
        ]);

        $this->actingAs($this->sender, 'student')
            ->get(route('siswa.finance.transactions.show', $tx))
            ->assertOk();
    }

    public function test_signed_shared_receipt_is_public_and_shows_full_transaction_details(): void
    {
        $tx = StudentCardTransaction::create([
            'card_id'          => $this->sourceCard->id,
            'type'             => 'topup',
            'amount'           => 25000,
            'balance_before'   => 1234,
            'balance_after'    => 5678,
            'reference_number' => 'PUBLIC-RECEIPT-001',
            'description'      => 'Private transaction note',
        ]);

        $url = URL::signedRoute('siswa.finance.transactions.share', ['transaction' => $tx]);

        $this->get($url)
            ->assertOk()
            ->assertSee('PUBLIC-RECEIPT-001')
            ->assertSee('25.000')
            ->assertSee('CARD-SRC-001')
            ->assertSee('1.234')
            ->assertSee('5.678')
            ->assertSee('Private transaction note')
            ->assertSee($this->sender->nama_lengkap);
    }

    public function test_shared_receipt_rejects_unsigned_requests(): void
    {
        $tx = StudentCardTransaction::create([
            'card_id'          => $this->sourceCard->id,
            'type'             => 'topup',
            'amount'           => 25000,
            'balance_before'   => 50000,
            'balance_after'    => 75000,
            'reference_number' => 'UNSIGNED-RECEIPT-001',
        ]);

        $this->get(route('siswa.finance.transactions.share', $tx))
            ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Test 8: Proses transfer tanpa session (sesi expire) → redirect
    // ---------------------------------------------------------------
    public function test_process_transfer_without_session_redirects(): void
    {
        $this->actingAs($this->sender, 'student')
            ->post(route('siswa.finance.transfer.process'), [
                'pin' => '111111',
            ])->assertRedirect(route('siswa.finance.transfer'));
    }

    // ---------------------------------------------------------------
    // Test 9: Balance saldo tidak boleh negatif setelah transfer
    // ---------------------------------------------------------------
    public function test_balance_cannot_go_negative(): void
    {
        // Saldo sourceCard = 100000, coba transfer tepat 100000
        $this->doTransfer(100000, '111111');

        $this->assertSame(0.0, (float) $this->sourceCard->fresh()->balance);
        $this->assertGreaterThanOrEqual(0, $this->sourceCard->fresh()->balance);
    }

    // ---------------------------------------------------------------
    // Test 10: Transfer butuh auth siswa
    // ---------------------------------------------------------------
    public function test_transfer_form_requires_student_auth(): void
    {
        $this->get(route('siswa.finance.transfer'))
            ->assertRedirect(); // redirect ke login (URL bisa bervariasi tergantung guard config)
    }
}
