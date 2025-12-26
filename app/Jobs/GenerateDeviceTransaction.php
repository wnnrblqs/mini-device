<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDeviceTransaction implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $deviceId;
    public $deviceType;

    /**
     * Create a new job instance.
     */
    public function __construct($deviceId, $deviceType)
    {
        $this->deviceId = $deviceId;
        $this->deviceType = $deviceType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if device is still active
        $device = Device::find($this->deviceId);
        
        if (!$device || $device->device_status !== 'Active') {
            // Device is no longer active, stop generating transactions
            return;
        }

        // Generate and create transaction
        Transaction::create([
            'device_id' => $this->deviceId,
            'transaction_details' => $this->generateTransactionDetails($this->deviceType)
        ]);

        // Update device last activity
        $device->device_last_activity = now();
        $device->save();

        // Schedule next transaction (random delay between 1-5 seconds)
        $delay = rand(1, 5);
        GenerateDeviceTransaction::dispatch($this->deviceId, $this->deviceType)
            ->delay(now()->addSeconds($delay));
    }

    /**
     * Generate transaction details based on device type
     */
    private function generateTransactionDetails($device_type)
    {
        $users = ['Ahmad', 'Ali', 'Mohammad', 'Hassan', 'Osama'];
        
        // Normalize device type for comparison (case-insensitive, handle spaces)
        $normalized = strtolower(str_replace(' ', '', $device_type));

        switch ($normalized) {
            case 'accesscontroller':
            case 'access controller':
                return 'CardID: C' . rand(10000000, 99999999)
                . ' - User: ' . $users[array_rand($users)];
            case 'facerecognitionreader':
            case 'face recognition reader':
                return 'Face Match: ' . $users[array_rand($users)]
                . ' - Confidence: ' . rand(80, 100) . '%';
            case 'anpr':
                return 'Plate No: '
                     . chr(rand(65, 90)) . rand(1000, 9999) . chr(rand(65, 90));

            default:
                return 'Unknown Transaction';
        }
    }
}
