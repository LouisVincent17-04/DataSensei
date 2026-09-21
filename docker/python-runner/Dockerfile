FROM python:3.13-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1 \
    PYTHONNOUSERSITE=1 \
    MPLBACKEND=Agg \
    MPLCONFIGDIR=/tmp/matplotlib \
    XDG_CACHE_HOME=/tmp/.cache \
    OMP_NUM_THREADS=1 \
    OPENBLAS_NUM_THREADS=1 \
    MKL_NUM_THREADS=1 \
    NUMEXPR_NUM_THREADS=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        gcc \
        g++ \
        pkg-config \
        libfreetype6 \
        libfreetype6-dev \
        libpng16-16 \
        libpng-dev \
        libjpeg62-turbo \
        zlib1g \
        fonts-dejavu-core \
        fontconfig \
    && python -m pip install --upgrade pip setuptools wheel \
    && pip install --no-cache-dir \
        # Pinned to the release lines every lesson example was run against.
        # Unpinned, a rebuild silently pulled pandas 3, whose import reads the
        # time zone database and failed inside the sandbox.
        "numpy~=2.4.0" \
        "pandas~=3.0.0" \
        "matplotlib~=3.11.0" \
        packaging \
        pillow \
        "scipy~=1.17.0" \
        scikit-learn==1.8.0 \
        joblib \
        threadpoolctl \
        # Used by the visualisation, statistics and time-series lessons.
        "seaborn~=0.13.2" \
        "statsmodels~=0.15.0" \
        # Time zone data for pandas, independent of what the base image ships.
        tzdata \
    && apt-get purge -y --auto-remove \
        gcc \
        g++ \
        pkg-config \
        libfreetype6-dev \
        libpng-dev \
    && rm -rf /var/lib/apt/lists/* \
    && useradd --create-home --uid 1000 --shell /usr/sbin/nologin runner \
    && mkdir -p /opt/datasensei /workspace /workspace/datasensei_outputs /tmp/matplotlib /tmp/.cache \
    && chown -R runner:runner /workspace /tmp/matplotlib /tmp/.cache

COPY datasensei_runner.py /opt/datasensei/datasensei_runner.py

# Tells Laravel this image understands the warm-sandbox protocol
# (--standby, JSON jobs, input() without replay). Keep in sync with
# RUNNER_PROTOCOL in datasensei_runner.py and PythonWarmSandbox.php.
LABEL datasensei.runner.protocol="4"

RUN chmod 0555 /opt/datasensei/datasensei_runner.py

WORKDIR /workspace

USER runner

ENTRYPOINT ["python", "/opt/datasensei/datasensei_runner.py"]
CMD ["/workspace/main.py"]