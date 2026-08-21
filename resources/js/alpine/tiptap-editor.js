import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import ImageAlign from "../tiptap/extensions/ImageAlign.js";
import ClearFloat from "../tiptap/extensions/ClearFloat.js";
import Placeholder from "@tiptap/extension-placeholder";
import CharacterCount from "@tiptap/extension-character-count";
import Typography from "@tiptap/extension-typography";
import TextAlign from "@tiptap/extension-text-align";
import Highlight from "@tiptap/extension-highlight";
import {
    Table,
    TableRow,
    TableCell,
    TableHeader,
} from "@tiptap/extension-table";
import Youtube from "@tiptap/extension-youtube";

/**
 * Initialize a TipTap editor on a target element and keep it in sync with
 * a Livewire property through `wire:ignore`.
 *
 * IMPORTANTE: la instancia de `Editor` vive en una variable de closure
 * (`editor`), NO como propiedad reactiva de Alpine (`this.editor`).
 * Alpine envuelve las propiedades del componente en Proxies, y si
 * ProseMirror aplica una transacción sobre una instancia proxificada,
 * lanza "RangeError: Applying a mismatched transaction". Ver:
 * https://tiptap.dev/docs/editor/getting-started/install/alpine
 *
 * Usage (Blade):
 *   <div
 *       x-data="tiptapEditor({
 *           endpoint: '/dashboard/blog/images',
 *           csrf: document.querySelector('meta[name=csrf-token]').content,
 *           initial: $wire.body,
 *           slug: $wire.slug,
 *       })"
 *       x-init="mount($el, $wire)"
 *       wire:ignore
 *       wire:key="post-body-editor"
 *   >
 *       <div data-tiptap-target="toolbar" class="..."> ... </div>
 *       <div data-tiptap-target="editor"></div>
 *   </div>
 */
export default (config = {}) => {
    // Vive fuera del objeto reactivo de Alpine a propósito.
    let editor = null;

    return {
        endpoint: config.endpoint ?? "",
        csrf: config.csrf ?? "",
        initial: config.initial ?? "",
        slug: config.slug ?? "",
        _toolbarEl: null,
        _clickHandler: null,
        _mousedownHandler: null,
        _morphHandler: null,
        _syncTimeout: null,
        _rootEl: null,

        // Toolbar reactive state — driven by editor.isActive()
        isBold: false,
        isItalic: false,
        isUnderline: false,
        isH2: false,
        isH3: false,
        isBulletList: false,
        isOrderedList: false,
        isBlockquote: false,
        isLink: false,
        isHighlight: false,
        isTable: false,
        isImage: false,
        imageAlign: null,
        isClearFloat: false,
        textAlign: "left",
        charCount: 0,

        mount(rootEl, wire) {
            this.destroy();

            this._rootEl = rootEl;
            this._morphHandler = (event) => {
                if (event.detail?.el === rootEl) {
                    this.destroy();
                }
            };
            document.addEventListener("morph.removed", this._morphHandler);

            editor = new Editor({
                element: rootEl.querySelector('[data-tiptap-target="editor"]'),
                extensions: [
                    StarterKit.configure({
                        paragraph: false,
                        link: {
                            openOnClick: false,
                            autolink: true,
                            protocols: ["http", "https", "mailto"],
                        },
                    }),
                    ClearFloat,
                    ImageAlign.configure({
                        inline: false,
                        allowBase64: false,
                        resize: {
                            enabled: true,
                            directions: [
                                "left",
                                "right",
                                "bottom-right",
                                "bottom-left",
                            ],
                            minWidth: 50,
                            minHeight: 50,
                            alwaysPreserveAspectRatio: true,
                        },
                    }),
                    Placeholder.configure({
                        placeholder: "Escribe el contenido del post...",
                    }),
                    CharacterCount,
                    Typography,
                    TextAlign.configure({
                        types: ["heading", "paragraph"],
                    }),
                    Highlight,
                    Table.configure({ resizable: true }),
                    TableRow,
                    TableCell,
                    TableHeader,
                    Youtube.configure({ controls: true, nocookie: true }),
                ],
                content: this.initial || "",
                editorProps: {
                    attributes: {
                        class: "prose dark:prose-invert max-w-none min-h-[300px] focus:outline-none",
                    },
                },
                onUpdate: ({ editor: ed }) => {
                    this.charCount = ed.storage.characterCount.characters();
                    this.scheduleSync(wire, ed);
                },
                onSelectionUpdate: () => this.updateToolbarState(),
                onTransaction: () => this.updateToolbarState(),
            });

            // Sync toolbar state after initial creation.
            this.updateToolbarState();

            this.bindToolbar(rootEl, wire);
        },

        /**
         * Read editor.isActive() for every toggle command and expose the
         * result as Alpine-reactive properties so :class bindings update.
         */
        updateToolbarState() {
            if (!editor) {
                return;
            }

            this.isBold = editor.isActive("bold");
            this.isItalic = editor.isActive("italic");
            this.isUnderline = editor.isActive("underline");
            this.isH2 = editor.isActive("heading", { level: 2 });
            this.isH3 = editor.isActive("heading", { level: 3 });
            this.isBulletList = editor.isActive("bulletList");
            this.isOrderedList = editor.isActive("orderedList");
            this.isBlockquote = editor.isActive("blockquote");
            this.isLink = editor.isActive("link");
            this.isHighlight = editor.isActive("highlight");
            this.isTable = editor.isActive("table");

            // Detect image selection via ProseMirror selection directly.
            // editor.isActive("image") may not work with resize wrappers.
            this.isImage = false;
            this.imageAlign = null;
            const { from, to } = editor.state.selection;
            editor.state.doc.nodesBetween(from, to, (node) => {
                if (node.type.name === "image") {
                    this.isImage = true;
                    this.imageAlign = node.attrs.align ?? null;
                }
            });

            this.textAlign = editor.isActive({ textAlign: "center" })
                ? "center"
                : editor.isActive({ textAlign: "right" })
                  ? "right"
                  : "left";

            this.isClearFloat = editor.isActive("paragraph", {
                clearFloat: true,
            });
        },

        bindToolbar(rootEl, wire) {
            const bar = rootEl.querySelector('[data-tiptap-target="toolbar"]');
            if (!bar) {
                return;
            }

            this._toolbarEl = bar;

            // Evita que el navegador le robe el foco/selección al editor
            // antes de que se ejecute el comando.
            this._mousedownHandler = (event) => {
                if (event.target.closest("button[data-cmd]")) {
                    event.preventDefault();
                }
            };
            bar.addEventListener("mousedown", this._mousedownHandler);

            this._clickHandler = (event) => {
                const btn = event.target.closest("button[data-cmd]");
                if (!btn || !editor) {
                    return;
                }
                event.preventDefault();
                this.runCommand(btn, wire);
            };
            bar.addEventListener("click", this._clickHandler);
        },

        runCommand(btn, wire) {
            const cmd = btn.dataset.cmd;
            if (!editor) {
                return;
            }

            switch (cmd) {
                case "bold":
                    editor.chain().focus().toggleBold().run();
                    break;
                case "italic":
                    editor.chain().focus().toggleItalic().run();
                    break;
                case "underline":
                    editor.chain().focus().toggleUnderline().run();
                    break;
                case "h2":
                    editor.chain().focus().toggleHeading({ level: 2 }).run();
                    break;
                case "h3":
                    editor.chain().focus().toggleHeading({ level: 3 }).run();
                    break;
                case "ul":
                    editor.chain().focus().toggleBulletList().run();
                    break;
                case "ol":
                    editor.chain().focus().toggleOrderedList().run();
                    break;
                case "quote":
                    editor.chain().focus().toggleBlockquote().run();
                    break;
                case "link":
                    this.promptLink(editor);
                    break;
                case "image":
                    this.uploadInlineImage(editor, wire);
                    break;
                case "align-left":
                    editor.chain().focus().setTextAlign("left").run();
                    break;
                case "align-center":
                    editor.chain().focus().setTextAlign("center").run();
                    break;
                case "align-right":
                    editor.chain().focus().setTextAlign("right").run();
                    break;
                case "clear-float":
                    editor.chain().focus().toggleClearFloat().run();
                    break;
                case "set-image-align-left":
                    this.toggleImageAlign(editor, "left");
                    break;
                case "set-image-align-center":
                    this.toggleImageAlign(editor, "center");
                    break;
                case "set-image-align-right":
                    this.toggleImageAlign(editor, "right");
                    break;
                case "highlight":
                    editor.chain().focus().toggleHighlight().run();
                    break;
                case "table":
                    editor
                        .chain()
                        .focus()
                        .insertTable({ rows: 3, cols: 3, withHeaderRow: true })
                        .run();
                    break;
                case "youtube":
                    this.promptYoutube(editor);
                    break;
                case "image-alt":
                    this.promptImageAlt(editor);
                    break;
                default:
                    break;
            }
        },

        promptLink(ed) {
            const prev = ed.getAttributes("link").href ?? "";
            const url = window.prompt(window.__t.url, prev);
            if (url === null) {
                return;
            }
            if (url === "") {
                ed.chain().focus().unsetLink().run();
            } else {
                ed.chain()
                    .focus()
                    .extendMarkRange("link")
                    .setLink({ href: url })
                    .run();
            }
        },

        promptYoutube(ed) {
            const url = window.prompt(window.__t.youtubeUrl);
            if (url === null || url === "") {
                return;
            }
            if (!this.isAllowedYoutubeUrl(url)) {
                window.alert(
                    window.__t.youtubeUrlInvalid ??
                        "URL no válida. Solo se permiten enlaces de YouTube.",
                );
                return;
            }
            ed.commands.setYoutubeVideo({ src: url });
        },

        /**
         * Validate that the URL is a YouTube URL before inserting.
         */
        isAllowedYoutubeUrl(url) {
            try {
                const parsed = new URL(url);
                const host = parsed.hostname.replace(/^www\./, "");
                return ["youtube.com", "youtu.be", "youtube-nocookie.com"].some(
                    (allowed) =>
                        host === allowed || host.endsWith("." + allowed),
                );
            } catch {
                return false;
            }
        },

        promptImageAlt(ed) {
            if (!ed.isActive("image")) {
                window.alert(
                    window.__t.selectImageFirst ??
                        "Seleccioná una imagen primero.",
                );
                return;
            }
            const prevAlt = ed.getAttributes("image").alt ?? "";
            const alt = window.prompt(
                window.__t.imageAlt ?? "Texto alternativo de la imagen",
                prevAlt,
            );
            if (alt === null) {
                return;
            }
            ed.chain().focus().updateAttributes("image", { alt }).run();
        },

        toggleImageAlign(ed, align) {
            // Read current align from ProseMirror selection directly.
            let current = null;
            const { from, to } = ed.state.selection;
            ed.state.doc.nodesBetween(from, to, (node) => {
                if (node.type.name === "image") {
                    current = node.attrs.align ?? null;
                }
            });
            const next = current === align ? null : align;
            ed.chain().focus().setImageAlign(next).run();
        },

        async uploadInlineImage(ed, wire) {
            const input = document.createElement("input");
            input.type = "file";
            input.accept = "image/png,image/jpeg,image/webp";
            input.addEventListener("change", async () => {
                const file = input.files?.[0];
                if (!file) {
                    return;
                }
                const fd = new FormData();
                fd.append("upload", file);
                if (this.slug) {
                    fd.append("slugHint", this.slug);
                }
                try {
                    const res = await fetch(this.endpoint, {
                        method: "POST",
                        credentials: "same-origin",
                        headers: {
                            "X-CSRF-TOKEN": this.csrf,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                        body: fd,
                    });
                    if (!res.ok) {
                        let msg = `Upload failed (${res.status})`;
                        try {
                            const err = await res.json();
                            if (err.error?.message) {
                                msg = err.error.message;
                            } else if (err.errors?.upload?.[0]) {
                                msg = err.errors.upload[0];
                            }
                        } catch (_e) {
                            // Response was not JSON — keep the default msg.
                        }
                        window.alert(msg);
                        return;
                    }
                    const payload = await res.json();
                    if (!payload.url) {
                        return;
                    }
                    ed.chain()
                        .focus()
                        .setImage({ src: payload.url, alt: "" })
                        .run();
                } catch (err) {
                    window.alert(err?.message || window.__t.imageUploadFailed);
                }
            });
            input.click();
        },

        scheduleSync(wire, ed) {
            clearTimeout(this._syncTimeout);
            this._syncTimeout = setTimeout(() => {
                wire.set("body", ed.getHTML(), false);
            }, 400);
        },

        destroy() {
            clearTimeout(this._syncTimeout);
            this._syncTimeout = null;

            if (this._morphHandler) {
                document.removeEventListener(
                    "morph.removed",
                    this._morphHandler,
                );
                this._morphHandler = null;
            }

            if (this._toolbarEl) {
                if (this._clickHandler) {
                    this._toolbarEl.removeEventListener(
                        "click",
                        this._clickHandler,
                    );
                }
                if (this._mousedownHandler) {
                    this._toolbarEl.removeEventListener(
                        "mousedown",
                        this._mousedownHandler,
                    );
                }
                this._toolbarEl = null;
                this._clickHandler = null;
                this._mousedownHandler = null;
            }

            if (editor) {
                // Tiptap's destroy() calls removeAllListeners() internally,
                // so the selectionUpdate/transaction listeners are cleaned up.
                editor.destroy();
                editor = null;
            }

            this._rootEl = null;
        },
    };
};
