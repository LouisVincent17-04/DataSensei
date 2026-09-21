<?php

// One PHP process = one concurrent learner. Used by PythonWarmSandboxDockerTest.
require __DIR__.'/../../../../vendor/autoload.php';
$app = require __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config([
    'cache.default' => 'file',
    'code_execution.python.driver' => 'docker',
    'code_execution.python.warm.enabled' => true,
]);

$id = 'worker-'.(int) ($argv[1] ?? 0);
$result = app(App\Services\PythonSandboxService::class)->runInline("import time\ntime.sleep(0.3)\nprint('".$id."')\n");

echo $result['stdout'];
if ($result['stdout'] === '') { fwrite(STDERR, json_encode($result)); }
