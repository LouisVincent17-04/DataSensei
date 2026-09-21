# Warm Python sandbox and IDE run progress

## How a run works now

Rule of the design: **one step of a run = one Docker call**, and that call is a
`docker exec` into a container that already exists. The classic runner pays a
`docker run` (create + start + stop a container) for every step, and replays the
whole program for every `input()` answer.

1. `python-sandbox:pool maintain` (scheduler, plus a background refill after each
   claim) is the only place that lists, creates and waits for containers. It
   creates them one at a time, waits until each has imported numpy, pandas and
   matplotlib, and writes the list of ready containers to the cache.
2. Run: Laravel takes a name from that list and calls
   `docker exec -i <container> python ... --attach` with the job on stdin. The
   helper claims the container (a file created with O_EXCL, so two requests can
   never share one), hands the job to the waiting runner, waits until the
   program asks for input or ends, and prints one JSON reply.
3. `input()`: the container stays alive. The next request is again one
   `docker exec`: append the answer, wait, reply. The same process continues.
4. Program output is written to files in the container's own `/tmp` and read by
   the helper. `docker logs` is not used on the request path.
5. If no container is ready, a program without `input(` goes straight to the
   classic runner (one call, same as before). A program with `input(` gets a
   container created on the spot, because that still saves a replay per answer.
6. Any warm-path failure switches the warm path off for
   `PYTHON_SANDBOX_WARM_SUSPEND_MINUTES` (10) and the classic runner takes every
   run. `python-sandbox:pool status` shows the reason.

The run progress card in the IDE is a CSS animation and a text update per frame.
It sends no requests and does not affect how long a run takes.

## Measure your machine

    php artisan python-sandbox:pool doctor

prints the time of one Docker call, a classic run and answer, and a warm run and
answer on this machine. If a warm line is slower than its classic line, set
`PYTHON_SANDBOX_WARM=false`.

## One-time setup (required for points 2 and 3)

The runner lives inside the Docker image, so rebuild it once:

    docker build -t datasensei-python-runner:latest docker/python-runner
    php artisan optimize:clear
    php artisan python-sandbox:pool maintain

Until the image is rebuilt the application notices the old image (no
`datasensei.runner.protocol=4` label) and keeps using the classic path, so
nothing breaks.

Keep `start-scheduler.bat` running: it refills the pool every minute. The pool is
also refilled in the background right after a container is claimed.

## Settings (.env)

    PYTHON_SANDBOX_WARM=true          # false = classic path only
    PYTHON_SANDBOX_WARM_POOL=2        # standby containers (0-16)
    PYTHON_SANDBOX_WARM_PRELOAD=numpy,pandas,matplotlib.pyplot,scipy.stats,sklearn.model_selection,sklearn.preprocessing,sklearn.linear_model,sklearn.ensemble,sklearn.cluster,sklearn.metrics,seaborn
    PYTHON_SANDBOX_INPUT_IDLE=300     # seconds a program may wait in input()

RAM: about 10 MB per idle container without preload. With the default preload list (numpy, pandas,
matplotlib, scipy.stats, the common scikit-learn packages, seaborn) a standby process measured about
190 MB here, so two containers are roughly 400 MB. In exchange a scikit-learn lesson that spends about
3 CPU-seconds on imports alone starts instantly. Shorten `PYTHON_SANDBOX_WARM_PRELOAD` on a small machine.

`PYTHON_SANDBOX_CPUS=0.50` in your `.env` gives each program half a core, which
makes the interpreter and every import take twice as long. `1.00` or `2.00` is a
large, free speed-up if the machine has cores to spare.

## Commands

    php artisan python-sandbox:pool status     # list standby and running containers
    php artisan python-sandbox:pool maintain   # top up, remove expired ones
    php artisan python-sandbox:pool clear      # remove all of them, reset the suspension
    php artisan python-sandbox:pool doctor     # measure classic vs warm on this machine

## Isolation

Standby containers are built by the same function as a classic run
(`PythonSandboxService::dockerRunCommand`): no network, read-only root, all
capabilities dropped, no-new-privileges, memory/CPU/pid limits, UID 1000. They
have **no host mount at all**; the workspace travels as a JSON job over
`docker exec`. Each container runs exactly one program and is then removed.
Source policy checks (PHP and runner side) are unchanged.
