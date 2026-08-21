import navbar from "./alpine/navbar";
import "./utils/nav-theme";

document.addEventListener("alpine:init", () => {
    window.Alpine.data("navbar", navbar);

    window.Alpine.data("fadeInOnScroll", () => ({
        init() {
            const el = this.$el;
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            el.classList.add("is-visible");
                            observer.unobserve(el);
                        }
                    });
                },
                { threshold: 0.15, rootMargin: "0px 0px -80px 0px" },
            );
            observer.observe(el);
        },
    }));
});

const linkToPosts = document.querySelector('[data-id="link-to-posts"]');
if (linkToPosts) {
    linkToPosts.addEventListener("mouseover", () => {
        document.body.classList.remove("bg-amc-blue");
        document.body.classList.add("bg-white");
    });
    linkToPosts.addEventListener("mouseout", () => {
        document.body.classList.add("bg-amc-blue");
        document.body.classList.remove("bg-white");
    });
}
