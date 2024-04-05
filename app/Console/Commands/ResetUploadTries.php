<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetUploadTries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-upload-tries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the upload tries for a user after 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('max_tries', '>', 0)
                    ->where('updated_at', '<', now()->subDays(30))
                    ->get();

        //reset the users.
        foreach ($users as $user) {
            $user->max_tries = 0;
            $user->save();
        }

        $this->info('Maximum tries reset successfully for ' . count($users) . ' users.');
    }
}
