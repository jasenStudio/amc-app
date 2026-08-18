import navbar from "./alpine/navbar";

document.addEventListener("alpine:init", () => {
    window.Alpine.data("navbar", navbar);
});
