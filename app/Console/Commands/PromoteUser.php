<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUser extends Command
{
    protected $signature = 'users:promote {email : Email address of the existing user}';

    protected $description = 'Grant super-admin access to an existing portal user';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user was found with that email address. Register the account first.');

            return self::FAILURE;
        }

        if ($user->isSuperAdmin()) {
            $this->info($user->email.' already has super-admin access.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Grant full super-admin access to '.$user->name.' ('.$user->email.')?')) {
            $this->info('No changes made.');

            return self::SUCCESS;
        }

        $user->update(['role' => 'super-admin']);
        $this->info('Super-admin access granted to '.$user->email.'.');

        return self::SUCCESS;
    }
}
