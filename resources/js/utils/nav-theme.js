// resources/js/nav-theme.js
(function () {
    const THEME_CLASS = { dark: "bg-amc-blue", light: "bg-white" };
    const baseTheme = document.body.classList.contains("bg-amc-blue")
        ? "dark"
        : "light";

    const nav = document.querySelector("nav[aria-label]");
    if (!nav) return;

    function setTheme(theme) {
        document.body.classList.remove(THEME_CLASS.dark, THEME_CLASS.light);
        document.body.classList.add(THEME_CLASS[theme]);
    }

    nav.addEventListener("mouseover", (e) => {
        const link = e.target.closest("[data-theme]");
        if (link) setTheme(link.dataset.theme);
    });

    nav.addEventListener("mouseout", (e) => {
        const link = e.target.closest("[data-theme]");
        if (link) setTheme(baseTheme);
    });
})();
