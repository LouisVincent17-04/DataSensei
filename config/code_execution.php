<?php

/*
 * A local Ollama model on CPU needs several seconds for the first request after
 * the model is unloaded (weights load + prompt eval + generation), but a review
 * fires on every Run and must never hold the panel for a minute.
 */
$ollamaTimeout = min(60, max(5, (int) env('OLLAMA_TIMEOUT', 30)));
$reviewTimeout = min($ollamaTimeout, max(5, (int) env('OLLAMA_REVIEW_TIMEOUT', 20)));

return [
    'python' => [
        /* Docker is the secure default in every environment. */
        'driver' => env('PYTHON_SANDBOX_DRIVER', 'docker'),
        /* How long the LEARNER'S OWN CODE may run. The runner starts this
           clock at their first statement, so container start-up and library
           imports are no longer billed to them. */
        'timeout_seconds' => (int) env('PYTHON_SANDBOX_TIMEOUT', 20),
        /* Extra wall clock the sandbox may spend starting before that clock
           begins. A cold "docker run" on Windows is routinely several seconds. */
        'startup_grace_seconds' => (int) env('PYTHON_SANDBOX_STARTUP_GRACE', 20),
        'max_concurrent_executions' => (int) env('PYTHON_SANDBOX_MAX_CONCURRENT', 8),
        'max_code_bytes' => (int) env('PYTHON_SANDBOX_MAX_CODE_BYTES', 50000),
        'max_stdin_bytes' => (int) env('PYTHON_SANDBOX_MAX_STDIN_BYTES', 10000),
        'max_stdout_bytes' => (int) env('PYTHON_SANDBOX_MAX_STDOUT_BYTES', 60000),
        'max_stderr_bytes' => (int) env('PYTHON_SANDBOX_MAX_STDERR_BYTES', 60000),
        'max_plot_bytes' => (int) env('PYTHON_SANDBOX_MAX_PLOT_BYTES', 1500000),
        'max_plots' => (int) env('PYTHON_SANDBOX_MAX_PLOTS', 4),
        'max_generated_file_bytes' => (int) env('PYTHON_SANDBOX_MAX_FILE_BYTES', 8 * 1024 * 1024),

        /*
         * Source-level policy. The container is the real boundary; this layer
         * only keeps lessons away from process and network APIs.
         */
        'policy' => [
            'allow_dynamic_execution' => (bool) env('PYTHON_ALLOW_DYNAMIC_EXECUTION', false),
        ],

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
            /* Half a core doubled every import: "import pandas" alone measured
               1.9s at 0.50 and 0.8s at 2.0. Lessons import pandas, numpy,
               matplotlib and scikit-learn, so the sandbox gets real cores and
               enough RAM to keep them resident. */
            'memory' => env('PYTHON_SANDBOX_MEMORY', '1g'),
            'memory_swap' => env('PYTHON_SANDBOX_MEMORY_SWAP', '1g'),
            'cpus' => env('PYTHON_SANDBOX_CPUS', '2.0'),
            'pids_limit' => (int) env('PYTHON_SANDBOX_PIDS_LIMIT', 64),
            'read_only_root' => (bool) env('PYTHON_SANDBOX_READ_ONLY_ROOT', true),
            'tmpfs_size' => env('PYTHON_SANDBOX_TMPFS_SIZE', '64m'),
            'workspace_tmpfs_size' => env('PYTHON_SANDBOX_WORKSPACE_TMPFS_SIZE', '32m'),
            'run_as_user' => env('PYTHON_SANDBOX_DOCKER_USER', '1000:1000'),
            'cap_drop_all' => (bool) env('PYTHON_SANDBOX_CAP_DROP_ALL', true),
            'no_new_privileges' => (bool) env('PYTHON_SANDBOX_NO_NEW_PRIVILEGES', true),
        ],

        /*
         * Warm sandbox: standby containers started ahead of time with the same
         * restrictions as a classic run and no host mount. It trades idle RAM
         * (roughly 10 MB per container, 70-120 MB with the preloaded
         * libraries) for speed: no "docker run" per execution and no replay of
         * the whole program for every input() answer. Needs a runner image built
         * from the current docker/python-runner folder; older images are
         * detected and keep using the classic path.
         */
        'warm' => [
            'enabled' => (bool) env('PYTHON_SANDBOX_WARM', true),
            'pool_size' => (int) env('PYTHON_SANDBOX_WARM_POOL', 3),
            'cpu_shares' => (int) env('PYTHON_SANDBOX_WARM_CPU_SHARES', 1024),
            /* After a warm-path failure every run uses the classic runner for
               this long, then the warm path is tried again. */
            'suspend_minutes' => (int) env('PYTHON_SANDBOX_WARM_SUSPEND_MINUTES', 10),
            'preload' => env('PYTHON_SANDBOX_WARM_PRELOAD', 'numpy,pandas,matplotlib.pyplot,scipy.stats,sklearn.model_selection,sklearn.preprocessing,sklearn.linear_model,sklearn.ensemble,sklearn.cluster,sklearn.metrics,seaborn'),
            /* How long a program may wait in input() before it is abandoned. */
            /* A claimed container must start the job within this many seconds,
               otherwise the classic runner takes the run. */
            'pickup_seconds' => (int) env('PYTHON_SANDBOX_WARM_PICKUP_SECONDS', 6),
            'input_idle_seconds' => (int) env('PYTHON_SANDBOX_INPUT_IDLE', 300),
            /* Standby containers are replaced after this long. */
            'standby_seconds' => (int) env('PYTHON_SANDBOX_STANDBY_SECONDS', 1800),
            'max_job_bytes' => (int) env('PYTHON_SANDBOX_WARM_MAX_JOB_BYTES', 6 * 1024 * 1024),
            'max_log_bytes' => (int) env('PYTHON_SANDBOX_WARM_MAX_LOG_BYTES', 16 * 1024 * 1024),
            /* Empty = detect. Set it when PHP runs as an Apache module. */
            'php_binary' => env('PYTHON_SANDBOX_PHP_BINARY', ''),
        ],
    ],

    /*
     * Straight-line beginner Python that ran cleanly is reviewed on this server
     * with no model call, which keeps the common lesson case instant.
     */
    'review' => [
        'fast_path' => (bool) env('CODE_REVIEW_FAST_PATH', true),
        'max_lines' => (int) env('CODE_REVIEW_FAST_PATH_MAX_LINES', 40),
        'max_chars' => (int) env('CODE_REVIEW_FAST_PATH_MAX_CHARS', 1500),
    ],

    'ollama' => [
        'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434/api/generate'),
        'model' => env('OLLAMA_MODEL', 'qwen2.5-coder:1.5b-instruct'),
        'timeout_seconds' => $ollamaTimeout,
        'review_timeout_seconds' => $reviewTimeout,
        'connect_timeout_seconds' => min(10, max(1, (int) env('OLLAMA_CONNECT_TIMEOUT', 3))),
        /* The browser must outlast the server, or it aborts a review that was
           about to arrive and reports a timeout that never happened. */
        'client_timeout_ms' => max(
            ($ollamaTimeout + 5) * 1000,
            (int) env('OLLAMA_CLIENT_TIMEOUT_MS', 0)
        ),
        /* -1 keeps the model in memory until Ollama stops. A duration such as
           "30m" unloads it after that much idle time, and the next review then
           waits several seconds while the model loads again. */
        'keep_alive' => env('OLLAMA_KEEP_ALIVE', '-1'),
        /* Load the model when the IDE or SQL Sandbox opens, every few minutes
           while it stays open, and from the scheduler. */
        'warmup' => (bool) env('OLLAMA_WARMUP', true),
        /* One local model should not be saturated by accidental parallel work. */
        'max_concurrent_requests' => min(2, max(1, (int) env('OLLAMA_MAX_CONCURRENT', 1))),
        'num_ctx' => min(8192, max(2048, (int) env('OLLAMA_NUM_CTX', 4096))),
        'review_num_predict' => min(512, max(96, (int) env('OLLAMA_REVIEW_NUM_PREDICT', 180))),
        'chat_num_predict' => min(768, max(128, (int) env('OLLAMA_CHAT_NUM_PREDICT', 320))),
        'max_code_chars' => 6000,
        'max_run_output_chars' => 1800,
        /* Accept the sandbox's bounded stderr, then compact it before prompting. */
        'max_raw_run_output_chars' => 65000,
        'max_history_chars' => 1800,
        'max_response_chars' => min(12000, max(1000, (int) env('OLLAMA_MAX_RESPONSE_CHARS', 6000))),
        'slow_request_ms' => min(30000, $ollamaTimeout * 1000),

        /*
         * When the model needs longer than the timeouts above, the review keeps
         * running in a background "code-review:process" command and the page
         * polls for the answer. 0 = no time limit for that background call.
         */
        'background_continuation' => (bool) env('OLLAMA_BACKGROUND_CONTINUATION', true),
        'background_timeout_seconds' => max(0, (int) env('OLLAMA_BACKGROUND_TIMEOUT', 0)),
        /* A background review that shows no result after this long is reported as stopped. */
        'background_stale_after_seconds' => max(60, (int) env('OLLAMA_BACKGROUND_STALE_AFTER', 1800)),
        'background_retention_seconds' => 7200,
        'background_poll_interval_ms' => 2000,
    ],
];
