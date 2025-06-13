<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUser extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create 
                            {name? : The full name of the user}
                            {email? : The email address of the user}
                            {--admin : Make the user an administrator}
                            {--password= : Set a specific password (if not provided, will be prompted)}
                            {--force : Skip confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->option('password');
        $isAdmin = $this->option('admin');
        $force = $this->option('force');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }

        // Get password if not provided
        if (!$password) {
            $password = $this->secret('Enter password for the user');
            $passwordConfirm = $this->secret('Confirm password');

            if ($password !== $passwordConfirm) {
                $this->error('Passwords do not match!');
                return 1;
            }
        }

        // Validate password strength
        $passwordValidator = Validator::make(['password' => $password], [
            'password' => ['required', Password::defaults()],
        ]);

        if ($passwordValidator->fails()) {
            $this->error('Password validation failed:');
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }

        // Show summary and ask for confirmation
        if (!$force) {
            $this->info('User details:');
            $this->table(['Field', 'Value'], [
                ['Name', $name],
                ['Email', $email],
                ['Admin', $isAdmin ? 'Yes' : 'No'],
            ]);

            if (!$this->confirm('Create this user?')) {
                $this->info('User creation cancelled.');
                return 0;
            }
        }

        try {
            // Create the user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => $isAdmin,
                'email_verified_at' => now(), // Auto-verify for CLI created users
            ]);

            $this->info("✅ User created successfully!");
            $this->table(['Field', 'Value'], [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Admin', $user->is_admin ? 'Yes' : 'No'],
                ['Created', $user->created_at->format('Y-m-d H:i:s')],
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to create user: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'What is the user\'s full name?',
            'email' => 'What is the user\'s email address?',
        ];
    }
}
