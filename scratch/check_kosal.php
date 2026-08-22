<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$u = User::where('email', 'kosalsensok065@gmail.com')->first();
if ($u) {
    echo "ID: {$u->id}\n";
    echo "Name: {$u->name}\n";
    echo "Email: {$u->email}\n";
    echo "Role: {$u->role}\n";
    echo "Status: {$u->status}\n";
    echo "Active: " . ($u->is_active ? 'YES' : 'NO') . "\n";
    echo "Password check 'Kosalsensokpk@12pk': " . (Hash::check('Kosalsensokpk@12pk', $u->password) ? 'MATCH' : 'NO MATCH') . "\n";
    echo "Password check 'password': " . (Hash::check('password', $u->password) ? 'MATCH' : 'NO MATCH') . "\n";
} else {
    echo "User kosalsensok065@gmail.com NOT FOUND\n";
}
