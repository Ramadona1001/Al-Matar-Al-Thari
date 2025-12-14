<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;

class ApprovePendingLoyaltyPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:approve-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve all pending loyalty points and convert them to balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to approve pending loyalty points...');

        $pendingTransactions = WalletTransaction::where('type', 'loyalty')
            ->where('status', 'pending')
            ->where('transaction_type', 'earned')
            ->with('wallet')
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('No pending loyalty points found.');
            return 0;
        }

        $this->info("Found {$pendingTransactions->count()} pending loyalty point transactions.");

        $approved = 0;
        foreach ($pendingTransactions as $transaction) {
            $wallet = $transaction->wallet;
            
            if (!$wallet) {
                $this->warn("Wallet not found for transaction ID: {$transaction->id}");
                continue;
            }

            // Approve the transaction
            $wallet->approveLoyaltyPoints($transaction->points);
            
            // Update transaction status
            $transaction->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            $approved++;
            $this->line("Approved {$transaction->points} points for user ID: {$wallet->user_id}");
        }

        $this->info("Successfully approved {$approved} loyalty point transactions.");
        return 0;
    }
}

