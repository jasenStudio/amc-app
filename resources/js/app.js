import navbar from "./alpine/navbar";
import tiptapEditor from "./alpine/tiptap-editor";

document.addEventListener("alpine:init", () => {
    window.Alpine.data("navbar", navbar);
    window.Alpine.data("tiptapEditor", tiptapEditor);
});
