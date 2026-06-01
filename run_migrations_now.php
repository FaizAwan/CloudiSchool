<?php

use Illuminate\Support\Facades\Artisan;

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'migrate', '--force']),
    new Symfony\Component\Console\Output\BufferedOutput
);

echo "Migration status: " . $status . "\n";
// You can also capture the output if you want
// echo $output->fetch();
echo "Done.";
