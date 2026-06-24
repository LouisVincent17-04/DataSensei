FROM python:3.12-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1 \
    MPLBACKEND=Agg \
    PYTHONNOUSERSITE=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends gcc g++ libfreetype6-dev libpng-dev \
    && pip install --no-cache-dir numpy pandas matplotlib \
    && apt-get purge -y --auto-remove gcc g++ \
    && rm -rf /var/lib/apt/lists/* \
    && useradd --create-home --uid 1000 --shell /usr/sbin/nologin runner \
    && mkdir -p /opt/datasensei /workspace \
    && chown -R runner:runner /workspace

COPY datasensei_runner.py /opt/datasensei/datasensei_runner.py
RUN chmod 0555 /opt/datasensei/datasensei_runner.py

WORKDIR /workspace
USER runner

ENTRYPOINT ["python", "/opt/datasensei/datasensei_runner.py"]
CMD ["/workspace/main.py"]
