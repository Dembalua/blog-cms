import './bootstrap';

window.quiz = ({ questionCount, overallMin, overallMax, results = [] }) => ({
    selected: {},
    revealed: false,
    get total() {
        let sum = 0;
        for (let i = 0; i < questionCount; i++) {
            const v = this.selected[i];
            if (typeof v === 'number' && !Number.isNaN(v)) sum += v;
        }
        return sum;
    },
    get allAnswered() {
        for (let i = 0; i < questionCount; i++) {
            if (typeof this.selected[i] !== 'number' || Number.isNaN(this.selected[i])) return false;
        }
        return true;
    },
    get markerPercent() {
        const range = overallMax - overallMin;
        if (range <= 0) return 50;
        const pct = ((this.total - overallMin) / range) * 100;
        return Math.max(0, Math.min(100, pct));
    },
    get currentResultHeading() {
        const t = this.total;
        const hit = results.find(r => t >= r.min && t <= r.max);
        return hit ? hit.heading : '';
    },
    reveal() {
        if (!this.allAnswered) return;
        this.revealed = true;
        this.$nextTick(() => {
            const el = this.$refs.results;
            if (el && typeof el.scrollIntoView === 'function') {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    },
    reset() {
        this.selected = {};
        this.revealed = false;
        this.$nextTick(() => {
            const el = this.$refs.top;
            if (el && typeof el.scrollIntoView === 'function') {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    },
});

(function () {
    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function nl2br(str) {
        return escapeHtml(str).replace(/\n/g, '<br>');
    }

    function buildQuizHtml(config) {
        const questions = Array.isArray(config.questions) ? config.questions : [];
        const results = Array.isArray(config.results) ? config.results : [];
        const ctaLabel = config.cta_label || 'Show result';
        const uid = 'quiz-' + Math.random().toString(36).slice(2, 8);

        const overallMin = results.length
            ? results.reduce((m, r) => Math.min(m, parseInt(r.min_points, 10) || 0), Infinity)
            : 0;
        const overallMax = results.length
            ? results.reduce((m, r) => Math.max(m, parseInt(r.max_points, 10) || 0), -Infinity)
            : 0;

        let html = '<span x-ref="top" aria-hidden="true"></span>';
        if (config.heading) html += `<h2 class="quiz-block__heading">${escapeHtml(config.heading)}</h2>`;
        if (config.intro) html += `<p class="quiz-block__intro">${nl2br(config.intro)}</p>`;

        html += '<div class="quiz-block__form">';
        questions.forEach((q, qi) => {
            const answers = q.answers || [];
            const maxPoints = answers.reduce((m, a) => Math.max(m, parseInt(a.points, 10) || 0), 0);

            html += `<fieldset class="quiz-block__question" role="radiogroup" aria-labelledby="${uid}-q${qi}-legend">`;
            html += `<legend id="${uid}-q${qi}-legend" class="quiz-block__question-text"><span class="quiz-block__question-number">${qi + 1}</span><span class="quiz-block__question-prose">${escapeHtml(q.question || '')}</span></legend>`;
            html += '<div class="quiz-block__answers">';
            answers.forEach((a) => {
                const points = parseInt(a.points, 10) || 0;
                const label = escapeHtml(a.label || '');

                let dotsHtml = '';
                for (let i = 1; i <= maxPoints; i++) {
                    const filled = i <= points ? ' is-filled' : '';
                    dotsHtml += `<span class="quiz-block__dot${filled}" aria-hidden="true"></span>`;
                }

                html += `<button type="button" class="quiz-block__answer" role="radio" :aria-checked="selected[${qi}] === ${points}" :class="{ 'is-selected': selected[${qi}] === ${points} }" :disabled="revealed" @click="selected[${qi}] = ${points}">`;
                html += `<span class="quiz-block__answer-label">${label}</span>`;
                html += `<span class="quiz-block__scale" aria-hidden="true">${dotsHtml}</span>`;
                html += '</button>';
            });
            html += '</div></fieldset>';
        });
        html += '</div>';

        html += '<div class="quiz-block__submit" x-show="!revealed">';
        html += `<button type="button" class="quiz-block__cta" @click="reveal()" :disabled="!allAnswered" :aria-disabled="!allAnswered">${escapeHtml(ctaLabel)}</button>`;
        html += '</div>';

        html += '<div class="quiz-block__results" x-ref="results" x-show="revealed" x-cloak x-transition.opacity.scale.95.duration.500ms>';

        if (results.length > 0) {
            html += '<span class="fade-rule quiz-block__results-rule" aria-hidden="true"></span>';
            html += '<div class="quiz-block__spectrum" aria-hidden="true">';
            html += '<div class="quiz-block__spectrum-track">';
            html += `<span class="quiz-block__spectrum-endpoint quiz-block__spectrum-endpoint--start">${overallMin}</span>`;
            html += `<span class="quiz-block__spectrum-endpoint quiz-block__spectrum-endpoint--end">${overallMax}</span>`;
            html += `<div class="quiz-block__spectrum-marker" :style="\`left: \${markerPercent}%\`">`;
            html += `<span class="quiz-block__spectrum-marker-label" x-text="currentResultHeading"></span>`;
            html += `<span class="quiz-block__spectrum-marker-value" x-text="total"></span>`;
            html += `</div>`;
            html += '</div>';
            html += '</div>';
        }

        results.forEach((r) => {
            const min = parseInt(r.min_points, 10) || 0;
            const max = parseInt(r.max_points, 10) || 0;
            html += `<article class="quiz-block__result" x-show="total >= ${min} && total <= ${max}">`;
            html += `<p class="quiz-block__result-kicker">Your result</p>`;
            html += `<h3 class="quiz-block__result-heading">${escapeHtml(r.heading || '')}</h3>`;
            html += `<p class="quiz-block__result-range">${min}&thinsp;&ndash;&thinsp;${max} points</p>`;
            html += `<p class="quiz-block__result-body">${nl2br(r.body || '')}</p>`;
            if (r.link_html) {
                html += `<p class="quiz-block__result-link">${r.link_html}</p>`;
            }
            html += '</article>';
        });

        html += '<div class="quiz-block__restart"><button type="button" class="quiz-block__restart-button" @click="reset()">Restart test</button></div>';
        html += '</div>';

        return html;
    }

    function initQuizBlocks() {
        document.querySelectorAll('[data-type="quiz"]:not([data-quiz-initialized])').forEach(placeholder => {
            placeholder.setAttribute('data-quiz-initialized', '');
            const configEl = placeholder.querySelector('.quiz-block__config');
            if (!configEl) return;
            let config;
            try {
                config = JSON.parse(configEl.textContent);
            } catch (e) {
                return;
            }
            const questionCount = Array.isArray(config.questions) ? config.questions.length : 0;
            const resultsArr = Array.isArray(config.results) ? config.results : [];
            const overallMin = resultsArr.length
                ? resultsArr.reduce((m, r) => Math.min(m, parseInt(r.min_points, 10) || 0), Infinity)
                : 0;
            const overallMax = resultsArr.length
                ? resultsArr.reduce((m, r) => Math.max(m, parseInt(r.max_points, 10) || 0), -Infinity)
                : 0;

            const resultsForAlpine = resultsArr.map(r => ({
                min: parseInt(r.min_points, 10) || 0,
                max: parseInt(r.max_points, 10) || 0,
                heading: r.heading || '',
            }));

            const wrapper = document.createElement('section');
            wrapper.className = 'quiz-block';
            wrapper.setAttribute('x-data', `quiz({ questionCount: ${questionCount}, overallMin: ${overallMin}, overallMax: ${overallMax}, results: ${JSON.stringify(resultsForAlpine)} })`);
            wrapper.innerHTML = buildQuizHtml(config);

            placeholder.replaceWith(wrapper);

            if (typeof Alpine !== 'undefined') {
                Alpine.initTree(wrapper);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initQuizBlocks);
    document.addEventListener('livewire:navigated', initQuizBlocks);
})();

window.contactFormModal = () => ({
    open: false,
    loading: false,
    success: false,
    error: null,
    name: '',
    email: '',
    message: '',
    token: '',
    widgetId: null,
    openModal() {
        this.open = true;
        document.body.style.overflow = 'hidden';
        this.$nextTick(() => {
            const siteKey = document.querySelector('meta[name="turnstile-site-key"]')?.content ?? '';
            if (siteKey && typeof turnstile !== 'undefined' && this.$refs.turnstile) {
                this.widgetId = turnstile.render(this.$refs.turnstile, {
                    sitekey: siteKey,
                    callback: (t) => { this.token = t; },
                });
            }
        });
    },
    closeModal() {
        this.open = false;
        document.body.style.overflow = '';
        this.success = false;
        this.error = null;
        this.name = '';
        this.email = '';
        this.message = '';
        this.token = '';
        if (this.widgetId !== null && typeof turnstile !== 'undefined') {
            turnstile.remove(this.widgetId);
            this.widgetId = null;
        }
    },
    async submit() {
        this.loading = true;
        this.error = null;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        try {
            const res = await fetch('/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    name: this.name,
                    email: this.email,
                    message: this.message,
                    'cf-turnstile-response': this.token,
                }),
            });
            if (res.ok) {
                this.success = true;
            } else {
                const d = await res.json();
                this.error = d.message ?? 'An error occurred.';
                if (typeof turnstile !== 'undefined' && this.widgetId !== null) {
                    turnstile.reset(this.widgetId);
                    this.token = '';
                }
            }
        } catch {
            this.error = 'Please try again later.';
        }
        this.loading = false;
    },
});

(function () {
    function initContactFormBlocks() {
        const privacyUrl = document.querySelector('meta[name="privacy-url"]')?.content ?? '';

        document.querySelectorAll('[data-type="contact-form"]:not([data-cf-initialized])').forEach(placeholder => {
            placeholder.setAttribute('data-cf-initialized', '');
            const buttonText = placeholder.textContent.trim();

            const wrapper = document.createElement('div');
            wrapper.setAttribute('x-data', 'contactFormModal()');

            wrapper.innerHTML = `
                <button
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary border border-primary rounded-lg hover:bg-primary hover:text-background transition-colors cursor-pointer"
                    @click="openModal()"
                >${buttonText}</button>

                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                    @click.self="closeModal()"
                    style="display:none"
                >
                    <div class="relative w-full max-w-lg bg-background border border-border rounded-xl shadow-xl p-6">
                        <button
                            type="button"
                            class="absolute top-4 right-4 text-muted hover:text-foreground transition-colors cursor-pointer"
                            @click="closeModal()"
                            aria-label="Close"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>

                        <h2 class="text-lg font-semibold mb-4">Contact</h2>

                        <div x-show="success" style="display:none">
                            <p class="text-sm text-foreground">Thank you! Your message has been sent successfully.</p>
                            <button
                                type="button"
                                class="mt-4 text-sm text-primary hover:underline cursor-pointer"
                                @click="closeModal()"
                            >Close</button>
                        </div>

                        <form x-show="!success" @submit.prevent="submit()" novalidate style="display:none">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1" for="cf-name">Name <span class="text-muted text-xs">(optional)</span></label>
                                <input
                                    id="cf-name"
                                    type="text"
                                    x-model="name"
                                    class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                    autocomplete="name"
                                    maxlength="100"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1" for="cf-email">Email <span class="text-red-500">*</span></label>
                                <input
                                    id="cf-email"
                                    type="email"
                                    x-model="email"
                                    required
                                    class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                    autocomplete="email"
                                    maxlength="255"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1" for="cf-message">Message <span class="text-red-500">*</span></label>
                                <textarea
                                    id="cf-message"
                                    x-model="message"
                                    required
                                    rows="5"
                                    class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                                    maxlength="2000"
                                ></textarea>
                            </div>

                            <div x-ref="turnstile" class="mb-4"></div>

                            ${privacyUrl ? `<p class="mb-4 text-xs text-muted">By submitting this form you agree to the processing of your data in accordance with our <a href="${privacyUrl}" class="underline hover:text-foreground" target="_blank" rel="noopener">privacy policy</a>.</p>` : ''}

                            <div x-show="error" class="mb-4 text-sm text-red-500" x-text="error" style="display:none"></div>

                            <button
                                type="submit"
                                class="w-full px-4 py-2 text-sm font-medium bg-primary text-background rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 cursor-pointer"
                                :disabled="loading"
                            >
                                <span x-show="!loading">Send message</span>
                                <span x-show="loading" style="display:none">Sending…</span>
                            </button>
                        </form>
                    </div>
                </div>
            `;

            placeholder.replaceWith(wrapper);

            if (typeof Alpine !== 'undefined') {
                Alpine.initTree(wrapper);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initContactFormBlocks);
    document.addEventListener('livewire:navigated', initContactFormBlocks);
})();

(function () {
    function initReveal() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const items = document.querySelectorAll('[data-reveal]');
        items.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.55s ease, transform 0.55s ease';
        });
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        items.forEach(el => observer.observe(el));
    }

    document.addEventListener('DOMContentLoaded', initReveal);
    document.addEventListener('livewire:navigated', initReveal);
})();
