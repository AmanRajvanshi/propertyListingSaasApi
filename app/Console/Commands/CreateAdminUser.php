<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
  protected $signature = 'make:admin-user';

  protected $description = 'Create the first admin user';

  public function handle(): int
  {
    $this->info('Creating admin user...');

    $name = $this->ask('Name');
    $email = $this->ask('Email');
    $password = $this->secret('Password');

    if (User::where('email', $email)->exists()) {
      $this->error('A user with this email already exists.');
      return 1;
    }

    $user = User::create([
      'name' => $name,
      'email' => $email,
      'password' => Hash::make($password),
      'type' => 'admin',
      'routes' => ['everything'],
      'email_verified_at' => now(),
    ]);

    $this->info("✅ Admin user '{$user->name}' created successfully!");
    return 0;
  }
}
