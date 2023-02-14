<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create admin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'unique:admins,email'],
            'password' => ['nullable','string', Password::min(8)->numbers()->mixedCase()],
        ]);

        if ($validator->fails()) {

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        if (is_null($password)) {
            $password = Str::random(8);
        }

        Admin::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        echo "Finished";
        return Command::SUCCESS;

    }


}
