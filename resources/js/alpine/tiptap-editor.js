import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Image from "@tiptap/extension-image";

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
 *       })"
 *       x-init="mount($el, $wire)"
 *       wire:ignore
 *       wire:key="post-body-editor"
 *   >
 *       <div data-tiptap-target="toolbar" class="..."> ... </div>
 *       <div data-tiptap-target="editor"></div>
 *   </div>
 */
export default () => {
    // Vive fuera del objeto reactivo de Alpine a propósito.
    let editor = null;

    return {
        endpoint: "",
        csrf: "",
        initial: "",
        _toolbarEl: null,
        _clickHandler: null,
        _mousedownHandler: null,
        _morphHandler: null,
        _rootEl: null,

        // Toolbar reactive state — driven by editor.isActive()
        isBold: false,
        isItalic: false,
        isH2: false,
        isH3: false,
        isBulletList: false,
        isOrderedList: false,
        isBlockquote: false,
        isLink: false,

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
                        link: {
                            openOnClick: false,
                            autolink: true,
                            protocols: ["http", "https", "mailto"],
                        },
                    }),
                    Image.configure({ inline: false, allowBase64: false }),
                ],
                content: this.initial || "",
                editorProps: {
                    attributes: {
                        class: "prose dark:prose-invert max-w-none min-h-[300px] focus:outline-none",
                    },
                },
                onUpdate: ({ editor: ed }) => {
                    wire.set("body", ed.getHTML());
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
            this.isH2 = editor.isActive("heading", { level: 2 });
            this.isH3 = editor.isActive("heading", { level: 3 });
            this.isBulletList = editor.isActive("bulletList");
            this.isOrderedList = editor.isActive("orderedList");
            this.isBlockquote = editor.isActive("blockquote");
            this.isLink = editor.isActive("link");
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
                try {
                    const res = await fetch(this.endpoint, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": this.csrf,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                        body: fd,
                    });
                    if (!res.ok) {
                        window.alert(window.__t.imageUploadFailed);
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
                    window.alert(window.__t.imageUploadFailed);
                }
            });
            input.click();
        },

        destroy() {
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
