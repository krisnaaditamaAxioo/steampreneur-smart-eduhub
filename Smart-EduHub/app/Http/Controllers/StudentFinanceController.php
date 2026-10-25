<?php

namespace App\Http\Controllers;

use App\Models\StudentCardRequest;
use App\Models\StudentCardTransaction;
use App\Models\StudentCard;
use App\Models\TransactionActionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class StudentFinanceController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $cards = $student->cards()->latest()->get();
        $requests = $student->cardRequests()->latest()->get();
        $transactions = $this->transactionsQuery($student)
            ->latest()
            ->limit(10)
            ->get();

        return view('siswa.finance.index', compact('student', 'cards', 'requests', 'transactions'));
    }

    public function history()
    {
        $student = Auth::guard('student')->user();
        $transactions = $this->transactionsQuery($student)
            ->latest()
            ->get();

        return view('siswa.finance.history', compact('student', 'transactions'));
    }

    public function showRequestCard()
    {
        $student = Auth::guard('student')->user();
        return view('siswa.finance.request-card', compact('student'));
    }

    public function storeRequestCard(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return back()->with('error', 'Anda harus login terlebih dahulu!');
        }

        $validated = $request->validate([
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'statement_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('statement_file')) {
            $path = $request->file('statement_file')->store('card-requests', 'public');
            $validated['statement_file'] = $path;
        }

        $validated['student_id'] = $student->id;
        StudentCardRequest::create($validated);

        return redirect()->route('siswa.finance')->with('success', 'Pengajuan kartu berhasil dikirim, menunggu verifikasi admin!');
    }

    public function showTransaction(StudentCardTransaction $transaction)
    {
        $student = Auth::guard('student')->user();
        $transaction = $this->ensureTransactionOwnership($transaction, $student->id);
        $shareUrl = URL::signedRoute('siswa.finance.transactions.share', ['transaction' => $transaction]);

        return view('siswa.finance.transaction-detail', compact('student', 'transaction', 'shareUrl'));
    }

    public function receipt(StudentCardTransaction $transaction)
    {
        $student = Auth::guard('student')->user();
        $transaction = $this->ensureTransactionOwnership($transaction, $student->id);

        return view('siswa.finance.receipt', compact('student', 'transaction'));
    }

    public function sharedReceipt(StudentCardTransaction $transaction)
    {
        $transaction->loadMissing(['card.student', 'relatedCard.student', 'performedBy']);

        return view('siswa.finance.receipt', [
            'student' => null,
            'transaction' => $transaction,
            'isPublicReceipt' => true,
        ]);
    }

    public function showTransferForm()
    {
        $student = Auth::guard('student')->user();
        $cards = $student->cards()->where('status', 'active')->get();

        if ($cards->isEmpty()) {
            return redirect()->route('siswa.finance')->with('error', 'Anda tidak memiliki kartu aktif untuk melakukan transfer.');
        }

        return view('siswa.finance.transfer', compact('student', 'cards'));
    }

    public function confirmTransfer(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'source_card_id' => 'required|exists:student_cards,id',
            'target_card_number' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        $sourceCard = $student->cards()->where('id', $validated['source_card_id'])->where('status', 'active')->first();
        if (!$sourceCard) {
            return back()->with('error', 'Kartu asal tidak valid atau tidak aktif.')->withInput();
        }

        if ($sourceCard->card_number === $validated['target_card_number']) {
            return back()->with('error', 'Tidak dapat melakukan transfer ke kartu yang sama.')->withInput();
        }

        if ($sourceCard->balance < $validated['amount']) {
            return back()->with('error', 'Saldo tidak mencukupi untuk melakukan transfer ini.')->withInput();
        }

        $targetCard = StudentCard::where('card_number', $validated['target_card_number'])->where('status', 'active')->first();
        if (!$targetCard) {
            return back()->with('error', 'Nomor kartu tujuan tidak ditemukan atau tidak aktif.')->withInput();
        }

        // Simpan data transfer ke session
        session()->put('transfer_data', [
            'source_card_id' => $validated['source_card_id'],
            'target_card_number' => $validated['target_card_number'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'target_name' => $targetCard->student->nama_lengkap,
            'source_card_number' => $sourceCard->card_number
        ]);

        return redirect()->route('siswa.finance.transfer.pin');
    }

    public function showPinForm()
    {
        if (!session()->has('transfer_data')) {
            return redirect()->route('siswa.finance.transfer')->with('error', 'Sesi transfer tidak valid atau telah kedaluwarsa.');
        }

        $transferData = session('transfer_data');
        return view('siswa.finance.transfer-pin', compact('transferData'));
    }

    public function processTransfer(Request $request)
    {
        $student = Auth::guard('student')->user();

        if (!session()->has('transfer_data')) {
            return redirect()->route('siswa.finance.transfer')->with('error', 'Sesi transfer tidak valid atau telah kedaluwarsa.');
        }

        $request->validate([
            'pin' => 'required|string',
        ]);

        // Validate PIN
        if ($student->pin !== $request->pin) {
            return back()->with('error', 'PIN yang Anda masukkan salah.');
        }

        $transferData = session('transfer_data');
        
        $sourceCard = $student->cards()->where('id', $transferData['source_card_id'])->where('status', 'active')->first();
        $targetCard = StudentCard::where('card_number', $transferData['target_card_number'])->where('status', 'active')->first();

        if (!$sourceCard || !$targetCard) {
            session()->forget('transfer_data');
            return redirect()->route('siswa.finance.transfer')->with('error', 'Kartu sumber atau tujuan tidak valid.');
        }

        if ($sourceCard->balance < $transferData['amount']) {
            session()->forget('transfer_data');
            return redirect()->route('siswa.finance.transfer')->with('error', 'Saldo tidak mencukupi saat memproses transaksi.');
        }

        try {
            DB::beginTransaction();

            // Deduct from source
            $sourceBalanceBefore = $sourceCard->balance;
            $sourceCard->balance -= $transferData['amount'];
            $sourceCard->save();

            // Add to target
            $targetBalanceBefore = $targetCard->balance;
            $targetCard->balance += $transferData['amount'];
            $targetCard->save();

            $referenceNumber = 'TRF-' . strtoupper(uniqid());

            // Record transaction for source (sender)
            $sourceTransaction = StudentCardTransaction::create([
                'card_id' => $sourceCard->id,
                'related_card_id' => $targetCard->id,
                'type' => 'transfer',
                'amount' => $transferData['amount'],
                'balance_before' => $sourceBalanceBefore,
                'balance_after' => $sourceCard->balance,
                'reference_number' => $referenceNumber,
                'description' => $transferData['description'] ?: 'Transfer ke ' . $targetCard->student->nama_lengkap,
            ]);

            // Record transaction for target (receiver)
            $targetTransaction = StudentCardTransaction::create([
                'card_id' => $targetCard->id,
                'related_card_id' => $sourceCard->id,
                'type' => 'transfer_receive',
                'amount' => $transferData['amount'],
                'balance_before' => $targetBalanceBefore,
                'balance_after' => $targetCard->balance,
                'reference_number' => $referenceNumber,
                'description' => 'Terima transfer dari ' . $student->nama_lengkap,
            ]);

            // Rekam riwayat pembuatan transaksi transfer (sumber & tujuan)
            TransactionActionHistory::record($sourceTransaction, 'created');
            TransactionActionHistory::record($targetTransaction, 'created');

            DB::commit();

            session()->forget('transfer_data');
            return redirect()->route('siswa.finance.transactions.receipt', $sourceTransaction->id)->with('success', 'Transfer saldo berhasil dilakukan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('siswa.finance.transfer')->with('error', 'Terjadi kesalahan sistem saat memproses transfer: ' . $e->getMessage());
        }
    }

    private function transactionsQuery($student)
    {
        return StudentCardTransaction::with(['card.student', 'relatedCard.student', 'performedBy'])
            ->whereIn('card_id', $student->cards()->pluck('id'));
    }

    private function ensureTransactionOwnership(StudentCardTransaction $transaction, int $studentId): StudentCardTransaction
    {
        $transaction->loadMissing(['card.student', 'relatedCard.student', 'performedBy']);

        if ($transaction->card?->student_id !== $studentId) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        return $transaction;
    }
}
