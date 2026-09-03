<script>
  (() => {
    const form = document.getElementById(@json($formId));
    if (!form) return;

    const endpoint = @json($autosaveUrl);
    let currentVersion = Number(@json((int) $draftVersion));
    let debounceTimer = null;
    let intervalTimer = null;
    let requestInFlight = false;
    let savePending = false;
    let stopped = false;

    const stop = () => {
      stopped = true;
      savePending = false;
      window.clearTimeout(debounceTimer);
      window.clearInterval(intervalTimer);
    };

    const save = async (keepalive = false) => {
      if (stopped) return;
      if (requestInFlight) {
        savePending = true;
        return;
      }

      requestInFlight = true;
      savePending = false;
      const requestedVersion = currentVersion + 1;
      const body = new FormData(form);
      body.set('client_version', String(requestedVersion));

      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          body,
          credentials: 'same-origin',
          keepalive,
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (response.status === 409 && (payload.expired || payload.completed)) {
          stop();
          return;
        }
        if (!response.ok) {
          throw new Error('Autosave request failed.');
        }

        currentVersion = Math.max(
          currentVersion,
          Number(payload.current_version || requestedVersion)
        );
      } catch (error) {
        // Final submission still contains the current form values. Keep the
        // version unchanged so the same snapshot can be retried safely.
      } finally {
        requestInFlight = false;
        if (savePending && !stopped) {
          window.setTimeout(() => save(false), 0);
        }
      }
    };

    const scheduleSave = () => {
      if (stopped) return;
      window.clearTimeout(debounceTimer);
      debounceTimer = window.setTimeout(() => save(false), 700);
    };

    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', scheduleSave);
    form.addEventListener('submit', stop);
    form.addEventListener('datasensei:final-submit', stop);

    intervalTimer = window.setInterval(() => save(false), 10000);
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'hidden') save(true);
    });
    window.addEventListener('beforeunload', () => save(true));
  })();
</script>
