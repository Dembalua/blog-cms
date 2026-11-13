function applyTheme() {
    var stored = localStorage.getItem('frontend-theme');
    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    var isDark = stored === 'dark' || (!stored && prefersDark);
    document.documentElement.classList.toggle('dark', isDark);
    return isDark;
}

document.addEventListener('alpine:init', function () {
    Alpine.store('theme', {
        isDark: applyTheme(),
        toggle: function () {
            document.documentElement.classList.add('theme-transition');
            this.isDark = !this.isDark;
            document.documentElement.classList.toggle('dark', this.isDark);
            localStorage.setItem('frontend-theme', this.isDark ? 'dark' : 'light');
            window.setTimeout(function () {
                document.documentElement.classList.remove('theme-transition');
            }, 350);
        }
    });
});

document.addEventListener('livewire:navigated', function () {
    var isDark = applyTheme();
    if (window.Alpine && Alpine.store('theme')) {
        Alpine.store('theme').isDark = isDark;
    }

    document.querySelectorAll('script[type="application/ld+json"][async]').forEach(function (el) {
        el.remove();
    });
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
    if (localStorage.getItem('frontend-theme')) return;
    document.documentElement.classList.toggle('dark', e.matches);
    if (window.Alpine && Alpine.store('theme')) {
        Alpine.store('theme').isDark = e.matches;
    }
});
