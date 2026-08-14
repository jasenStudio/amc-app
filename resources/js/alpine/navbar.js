const sectionIds = ['hero', 'about', 'services', 'projects', 'blog', 'contact'];

export default (initialSection = '') => ({
    open: false,
    activeSection: window.location.hash.substring(1) || initialSection,
    observer: null,
    hashListener: null,

    init() {
        const sections = sectionIds
            .map((id) => document.getElementById(id))
            .filter(Boolean);

        this.hashListener = () => {
            this.activeSection = window.location.hash.substring(1) || initialSection;
        };

        window.addEventListener('hashchange', this.hashListener);

        if (!sections.length || !('IntersectionObserver' in window)) {
            return;
        }

        this.observer = new IntersectionObserver((entries) => {
            const visibleSection = entries
                .filter((entry) => entry.isIntersecting)
                .sort((first, second) => first.boundingClientRect.top - second.boundingClientRect.top)[0];

            if (visibleSection) {
                this.activeSection = visibleSection.target.id;
            }
        }, { rootMargin: '-20% 0px -60% 0px', threshold: 0 });

        sections.forEach((section) => this.observer.observe(section));
    },

    destroy() {
        this.observer?.disconnect();

        if (this.hashListener) {
            window.removeEventListener('hashchange', this.hashListener);
        }
    },
});
