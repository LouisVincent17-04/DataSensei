<?php

return [
    'python' => [
        /* Docker is the secure default in every environment. */
        'driver' => env('PYTHON_SANDBOX_DRIVER', 'docker'),
        'timeout_seconds' => (int) env('PYTHON_SANDBOX_TIMEOUT', 10),
        'max_concurrent_executions' => (int) env('PYTHON_SANDBOX_MAX_CONCURRENT', 8),
        'max_code_bytes' => (int) env('PYTHON_SANDBOX_MAX_CODE_BYTES', 50000),
        'max_stdin_bytes' => (int) env('PYTHON_SANDBOX_MAX_STDIN_BYTES', 10000),
        'max_stdout_bytes' => (int) env('PYTHON_SANDBOX_MAX_STDOUT_BYTES', 60000),
        'max_stderr_bytes' => (int) env('PYTHON_SANDBOX_MAX_STDERR_BYTES', 60000),
        'max_plot_bytes' => (int) env('PYTHON_SANDBOX_MAX_PLOT_BYTES', 1500000),
        'max_plots' => (int) env('PYTHON_SANDBOX_MAX_PLOTS', 4),
        'max_generated_file_bytes' => (int) env('PYTHON_SANDBOX_MAX_FILE_BYTES', 8 * 1024 * 1024),

        'local' => [
            'binary' => env('PYTHON_BINARY', 'auto'),
            /* Explicitly unsafe and intended only for an isolated developer VM. */
            'allow_unsafe' => (bool) env('PYTHON_SANDBOX_ALLOW_UNSAFE_LOCAL', false),
            'runner' => base_path('docker/python-runner/datasensei_runner.py'),
        ],

        'docker' => [
            'binary' => env('PYTHON_SANDBOX_DOCKER_BINARY', 'docker'),
            'image' => env('PYTHON_SANDBOX_DOCKER_IMAGE', 'datasensei-python-runner:latest'),
            'network' => env('PYTHON_SANDBOX_DOCKER_NETWORK', 'none'),
            'memory' => env('PYTHON_SANDBOX_MEMORY', '512m'),
            'memory_swap' => env('PYTHON_SANDBOX_MEMORY_SWAP', '512m'),
            'cpus' => env('PYTHON_SANDBOX_CPUS', '0.50'),
            'pids_limit' => (int) env('PYTHON_SANDBOX_PIDS_LIMIT', 64),
            'read_only_root' => (bool) env('PYTHON_SANDBOX_READ_ONLY_ROOT', true),
            'tmpfs_size' => env('PYTHON_SANDBOX_TMPFS_SIZE', '64m'),
            'workspace_tmpfs_size' => env('PYTHON_SANDBOX_WORKSPACE_TMPFS_SIZE', '32m'),
            'run_as_user' => env('PYTHON_SANDBOX_DOCKER_USER', '1000:1000'),
            'cap_drop_all' => (bool) env('PYTHON_SANDBOX_CAP_DROP_ALL', true),
            'no_new_privileges' => (bool) env('PYTHON_SANDBOX_NO_NEW_PRIVILEGES', true),
        ],
    ],

    'ollama' => [
        'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434/api/generate'),
        'model' => env('OLLAMA_MODEL', 'deepseek-coder'),
        'timeout_seconds' => (int) env('OLLAMA_TIMEOUT', 30),
        'max_concurrent_requests' => (int) env('OLLAMA_MAX_CONCURRENT', 4),
        'max_response_chars' => (int) env('OLLAMA_MAX_RESPONSE_CHARS', 5000),
    ],
];
