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
        numpy \
        pandas \
        matplotlib \
        packaging \
        pillow \
        scipy \
        scikit-learn==1.8.0 \
        joblib \
        threadpoolctl \
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

RUN chmod 0555 /opt/datasensei/datasensei_runner.py

WORKDIR /workspace

USER runner

ENTRYPOINT ["python", "/opt/datasensei/datasensei_runner.py"]
CMD ["/workspace/main.py"]