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

    const title = input.dataset.titleTarget
        ? document.querySelector(input.dataset.titleTarget)
        : null;

    if (file && title && title.value.trim() === '') {
        title.value = file.name.replace(/\.[^.]+$/, '').trim();
    }

    const fileName = input.dataset.fileNameTarget
        ? document.querySelector(input.dataset.fileNameTarget)
        : null;

    if (fileName) {
        fileName.textContent = file ? file.name : `Choose a photo. Maximum ${input.dataset.maxLabel}.`;
    }
}, true);

document.addEventListener('input', (event) => {
    const input = event.target.closest?.('[data-theme-color]');

    if (! input) {
        return;
    }

    document.documentElement.dataset.theme = 'custom';
    document.documentElement.style.setProperty(input.dataset.themeColor, input.value);
});

const searchForms = new WeakMap();

function searchState(form) {
    if (! searchForms.has(form)) {
        searchForms.set(form, {
            controller: null,
            timer: null,
            sequence: 0,
        });
    }

    return searchForms.get(form);
}

function setSearchOpen(input, results, open) {
    input.setAttribute('aria-expanded', open ? 'true' : 'false');
    results.hidden = ! open;
}

function closeSearch(form) {
    const input = form.querySelector('[data-search-input]');
    const results = form.querySelector('[data-search-results]');

    if (! input || ! results) {
        return;
    }

    setSearchOpen(input, results, false);
}

function appendHighlightedText(node, text, query) {
    const value = String(text ?? '');
    const needle = query.trim();

    if (value === '' || needle === '') {
        node.textContent = value;
        return;
    }

    const lowerValue = value.toLowerCase();
    const lowerNeedle = needle.toLowerCase();
    let offset = 0;

    while (offset < value.length) {
        const index = lowerValue.indexOf(lowerNeedle, offset);

        if (index === -1) {
            node.append(document.createTextNode(value.slice(offset)));
            return;
        }

        if (index > offset) {
            node.append(document.createTextNode(value.slice(offset, index)));
        }

        const mark = document.createElement('mark');
        mark.textContent = value.slice(index, index + needle.length);
        node.append(mark);
        offset = index + needle.length;
    }
}

function searchMessage(message) {
    const node = document.createElement('div');
    node.className = 'app-header__search-message';
    node.setAttribute('role', 'status');
    node.textContent = message;

    return node;
}

function renderSearchResults(form, query, results) {
    const input = form.querySelector('[data-search-input]');
    const container = form.querySelector('[data-search-results]');

    if (! input || ! container) {
        return;
    }

    container.replaceChildren();

    if (results.length === 0) {
        container.append(searchMessage('No matches'));
        setSearchOpen(input, container, true);
        return;
    }

    for (const result of results) {
        const link = document.createElement('a');
        link.className = 'app-header__search-result';
        link.href = result.url;
        link.setAttribute('role', 'option');

        const name = document.createElement('span');
        name.className = 'app-header__search-result-name';
        appendHighlightedText(name, result.name, query);
        link.append(name);

        const meta = document.createElement('span');
        meta.className = 'app-header__search-result-meta';
        appendHighlightedText(meta, result.type, query);
        link.append(meta);

        if (result.description) {
            const description = document.createElement('span');
            description.className = 'app-header__search-result-description';
            appendHighlightedText(description, result.description, query);
            link.append(description);
        }

        container.append(link);
    }

    setSearchOpen(input, container, true);
}

function searchFormFor(target) {
    return target.closest?.('[data-search-form]');
}

function requestSearch(form, input) {
    const state = searchState(form);
    const query = input.value.trim();
    state.sequence += 1;
    const sequence = state.sequence;

    window.clearTimeout(state.timer);

    if (state.controller) {
        state.controller.abort();
        state.controller = null;
    }

    if (query === '') {
        closeSearch(form);
        return;
    }

    state.timer = window.setTimeout(async () => {
        state.controller = new AbortController();

        try {
            const url = new URL(form.dataset.searchUrl, window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
                signal: state.controller.signal,
            });

            if (! response.ok) {
                throw new Error('Search request failed.');
            }

            const payload = await response.json();

            if (sequence !== searchState(form).sequence) {
                return;
            }

            renderSearchResults(form, query, payload.results ?? []);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            const container = form.querySelector('[data-search-results]');
            const currentInput = form.querySelector('[data-search-input]');

            if (container && currentInput) {
                container.replaceChildren(searchMessage('Search unavailable'));
                setSearchOpen(currentInput, container, true);
            }
        }
    }, 150);
}

document.addEventListener('input', (event) => {
    const input = event.target.closest?.('[data-search-input]');

    if (! input) {
        return;
    }

    const form = searchFormFor(input);

    if (form) {
        requestSearch(form, input);
    }
});

document.addEventListener('focusin', (event) => {
    const input = event.target.closest?.('[data-search-input]');

    if (! input || input.value.trim() === '') {
        return;
    }

    const form = searchFormFor(input);

    if (form) {
        requestSearch(form, input);
    }
});

document.addEventListener('keydown', (event) => {
    const form = searchFormFor(event.target);

    if (! form) {
        return;
    }

    const results = Array.from(form.querySelectorAll('.app-header__search-result'));
    const activeIndex = results.indexOf(document.activeElement);

    if (event.key === 'Escape') {
        closeSearch(form);
        form.querySelector('[data-search-input]')?.focus();
        return;
    }

    if (event.key === 'ArrowDown' && results.length > 0) {
        event.preventDefault();
        results[Math.min(activeIndex + 1, results.length - 1)].focus();
    }

    if (event.key === 'ArrowUp' && results.length > 0) {
        event.preventDefault();
        results[Math.max(activeIndex - 1, 0)].focus();
    }
});

document.addEventListener('click', (event) => {
    document.querySelectorAll('[data-search-form]').forEach((form) => {
        if (! form.contains(event.target)) {
            closeSearch(form);
        }
    });
});
