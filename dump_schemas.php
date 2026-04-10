<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schemas = [
    'clients' => \Illuminate\Support\Facades\DB::select('describe clients'),
    'projets' => \Illuminate\Support\Facades\DB::select('describe projets'),
    'contrats' => \Illuminate\Support\Facades\DB::select('describe contrats'),
];

file_put_contents('schemas_dump.json', json_encode($schemas, JSON_PRETTY_PRINT));
echo "Done\n";
