document.addEventListener('change', (event) => {
    const input = event.target.closest?.('input[type="file"][data-max-bytes]');

    if (! input) {
        return;
    }

    const file = input.files?.[0];
    const maxBytes = Number(input.dataset.maxBytes);
    const error = input.form?.querySelector('[data-upload-error]');
    const message = file && file.size > maxBytes
        ? `This photo is too large. The server limit is ${input.dataset.maxLabel}.`
        : '';

    input.setCustomValidity(message);

    if (error) {
        error.textContent = message;
        error.classList.toggle('hidden', message === '');
    }

    if (message) {
        input.reportValidity();
    }
}, true);
