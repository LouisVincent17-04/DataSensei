# Hybrid ML Deployment and Installation

## Requirements

- PHP 8.2 or later with PDO MySQL, Fileinfo, SimpleXML, XML, and Zip extensions.
- The existing project database. The added migrations avoid native JSON and remain compatible with the project’s older MySQL server.
- Laravel 12 dependencies installed.
- Docker Desktop, or a controlled local Python runtime for development.
- The bundled Docker image uses Python 3.13 and scikit-learn 1.8.0 to match the supplied benchmark artifacts.

## Install into an existing DataSensei project

Overwrite the supplied files, then from the DataSensei root run:

```powershell
php artisan optimize:clear
docker build -t datasensei-python-runner:latest docker/python-runner
php artisan ml:install
```

`ml:install` runs the migrations, registers ten built-in datasets, registers 31 read-only pretrained benchmark models, synchronizes the algorithm catalog, and clears cached configuration. It does not remove existing DataSensei data.

Add or update these `.env` values:

```env
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids
ML_QUEUE_CONNECTION=machine_learning
ML_QUEUE_NAME=machine-learning
ML_QUEUE_RETRY_AFTER=1200
ML_RUNNER_DRIVER=docker
ML_RUNNER_IMAGE=datasensei-python-runner:latest
ML_TRAINING_TIMEOUT=600
ML_PREDICTION_TIMEOUT=30
```

Start the web application normally. In a second terminal, run `start-ml-worker.bat` and leave the worker window open while training. The dedicated queue connection has a retry window longer than the worker timeout, preventing the same long-running training job from being processed twice.

## Local Python fallback

Use only for controlled development:

```env
ML_RUNNER_DRIVER=local
ML_LOCAL_PYTHON=python
```

Install matching packages:

```powershell
python -m pip install numpy pandas matplotlib scipy scikit-learn==1.8.0 joblib
```

Docker remains preferred because the runner uses no network, a read-only root filesystem, temporary writable output, resource limits, capability dropping, and process isolation.

## Rebuilding system assets

The system CSV files and benchmark artifacts are already included. To regenerate them in a matching trusted Python environment:

```powershell
php artisan ml:build-system-assets --python=python
php artisan db:seed --class="Database\Seeders\HybridMlSeeder"
```

Rebuilding replaces only `storage/app/ml/system`. It does not modify anything under `storage/app/ml/users`.

## Queue operations

Windows development:

```powershell
start-ml-worker.bat
php artisan queue:failed
php artisan queue:retry all
```

Manual worker command:

```powershell
php artisan queue:work machine_learning --queue=machine-learning --sleep=1 --tries=2 --timeout=900 --max-time=3600
```

A Supervisor example is included at `deploy/supervisor/datasensei-ml-worker.conf`. Update its project path and user before enabling it on Linux.

## Storage and backup

Keep `storage/app/ml` outside the public web root. Back up the database together with `storage/app/ml/users`; database records alone cannot restore Joblib files. System assets can be regenerated from the builder, but user artifacts cannot.

## Limits

Defaults are 10 MB per upload, 50,000 rows, 200 columns, 50 selected features, 600 seconds per training job, and 30 seconds per prediction. Adjust the `ML_*` variables only after measuring available memory and CPU.

## Operational checks

After installation:

```powershell
php artisan route:list --path=model-development
php artisan route:list --path=api/student/ml
php artisan queue:monitor machine-learning:100
```

Train a small built-in Iris model first. Confirm the progress page reaches 100%, a version is stored, charts open through authenticated routes, and a prediction can be generated.
