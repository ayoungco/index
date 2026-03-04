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

    root.classList.toggle('dark', resolved === 'dark');
    root.dataset.theme = resolved;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.textContent = resolved === 'dark' ? 'Switch to Light' : 'Switch to Dark';
        button.setAttribute('aria-label', resolved === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
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
