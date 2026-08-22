<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$users = User::all(['id', 'name', 'email', 'role', 'status', 'is_active']);
echo "Current Users Summary:\n";
foreach ($users as $u) {
    echo "- ID: {$u->id} | {$u->email} ({$u->name}) | Role: {$u->role} | Status: {$u->status} | Active: " . ($u->is_active ? 'YES' : 'NO') . "\n";
}
