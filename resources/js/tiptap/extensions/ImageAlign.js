import Image from "@tiptap/extension-image";

/**
 * Extended Image node that supports `data-align` attribute for
 * left / center / right alignment.
 */
const ImageAlign = Image.extend({
    name: "image",

    addAttributes() {
        const parent = this.parent?.() ?? {};

        return {
            ...parent,
            align: {
                default: null,
                parseHTML: (element) => element.getAttribute("data-align") || null,
                renderHTML: (attributes) => {
                    if (!attributes.align) {
                        return {};
                    }
                    return { "data-align": attributes.align };
                },
            },
        };
    },

    addCommands() {
        return {
            ...this.parent?.(),
            setImageAlign:
                (align) =>
                ({ state, dispatch }) => {
                    const { from, to } = state.selection;
                    let updated = false;

                    state.doc.nodesBetween(from, to, (node, pos) => {
                        if (node.type.name === "image" && dispatch) {
                            const tr = state.tr.setNodeMarkup(pos, undefined, {
                                ...node.attrs,
                                align,
                            });
                            dispatch(tr);
                            updated = true;
                        }
                    });

                    return updated;
                },
        };
    },
});

export default ImageAlign;
