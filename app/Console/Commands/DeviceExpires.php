<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Upload;
use Illuminate\Console\Command;

class DeviceExpires extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-expires';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $device = Device::where('expires_at', '<', now())->first();

        Upload::where('device_id', $device->id)->get()->each(function ($upload) use ($device) {
            $upload->disabled = true;
            $upload->save();
        });
    }
}
