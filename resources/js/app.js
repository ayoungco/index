const THEME_KEY = 'index_theme_preference';
const LEGACY_FLUX_THEME_KEY = 'flux.appearance';

function resolveTheme(preference) {
    if (preference === 'crt' || preference === 'paper' || preference === 'amber') {
        return preference;
    }

    if (preference === 'dark') {
        return 'crt';
    }

    if (preference === 'light') {
        return 'paper';
    }

    return 'crt';
}

function storedTheme() {
    try {
        return localStorage.getItem(THEME_KEY) || localStorage.getItem(LEGACY_FLUX_THEME_KEY);
    } catch (error) {
        return null;
    }
}

function persistTheme(theme) {
    try {
        localStorage.setItem(THEME_KEY, theme);
        localStorage.removeItem(LEGACY_FLUX_THEME_KEY);
    } catch (error) {
        // The visual switch should still work when storage is unavailable.
    }
}

function applyTheme(preference) {
    const resolved = resolveTheme(preference);
    const root = document.documentElement;

    root.classList.toggle('dark', resolved !== 'paper');
    root.dataset.theme = resolved;

    document.querySelectorAll('[data-theme-choice]').forEach((choice) => {
        const selected = choice.dataset.themeChoice === resolved;
        const label = choice.closest('.theme-choice');

        label?.classList.toggle('is-selected', selected);
        choice.checked = selected;
        choice.setAttribute('aria-checked', selected ? 'true' : 'false');
    });
}

function setTheme(theme) {
    const next = resolveTheme(theme);

    persistTheme(next);
    applyTheme(next);
}

function bindThemeControls() {
    document.querySelectorAll('[data-theme-choice]').forEach((button) => {
        if (button.dataset.themeBound === '1') {
            return;
        }

        button.dataset.themeBound = '1';
        button.addEventListener('change', () => setTheme(button.dataset.themeChoice));
    });
}

function initializeTheme() {
    bindThemeControls();
    applyTheme(storedTheme());
}

document.addEventListener('DOMContentLoaded', initializeTheme);
document.addEventListener('livewire:navigated', initializeTheme);
