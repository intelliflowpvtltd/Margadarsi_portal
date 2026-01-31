<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Database: " . env('DB_DATABASE') . "\n";
echo "Host: " . env('DB_HOST') . "\n";

$users = User::withTrashed()->get();

echo "Total Users (including trashed): " . $users->count() . "\n";
echo str_repeat("-", 50) . "\n";
foreach ($users as $user) {
    $status = $user->deleted_at ? "[DELETED]" : "[ACTIVE]";
    echo "ID: {$user->id} {$status}\n";
    echo "Name: {$user->full_name}\n";
    echo "Email: {$user->email}\n";
    echo "Hash Check (password123): " . (Hash::check('password123', $user->password) ? 'PASS' : 'FAIL') . "\n";
    echo str_repeat("-", 50) . "\n";
}
