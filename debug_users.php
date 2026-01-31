<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::with('role', 'company')->get();

echo "Total Users: " . $users->count() . "\n";
echo str_repeat("-", 50) . "\n";
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->full_name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: " . ($user->role->name ?? 'N/A') . "\n";
    echo "Company: " . ($user->company->name ?? 'N/A') . "\n";
    echo "Is Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
    echo str_repeat("-", 50) . "\n";
}
