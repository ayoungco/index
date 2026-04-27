const THEME_KEY = 'index_theme_preference';
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

function resolveTheme(preference) {
    if (preference === 'dark' || preference === 'light') {
        return preference;
    }

    return prefersDark.matches ? 'dark' : 'light';
}

function applyTheme(preference) {
    const resolved = resolveTheme(preference);
    const root = document.documentElement;
    const isDark = resolved === 'dark';

    root.classList.toggle('dark', isDark);
    root.dataset.theme = resolved;

    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.setAttribute('aria-checked', isDark ? 'true' : 'false');
        toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');

        const label = toggle.querySelector('[data-theme-toggle-label]');

        if (label) {
            label.textContent = isDark ? 'Light mode' : 'Dark mode';
        }
    });
}

function toggleTheme() {
    const current = resolveTheme(localStorage.getItem(THEME_KEY));
    const next = current === 'dark' ? 'light' : 'dark';

    localStorage.setItem(THEME_KEY, next);
    applyTheme(next);
}

function bindThemeToggles() {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        if (button.dataset.themeBound === '1') {
            return;
        }

        button.dataset.themeBound = '1';
        button.addEventListener('click', toggleTheme);
    });
}

function initializeTheme() {
    bindThemeToggles();
    applyTheme(localStorage.getItem(THEME_KEY));
}

prefersDark.addEventListener('change', () => {
    if (! localStorage.getItem(THEME_KEY)) {
        applyTheme(null);
    }
});

document.addEventListener('DOMContentLoaded', initializeTheme);
document.addEventListener('livewire:navigated', initializeTheme);
