import Paragraph from "@tiptap/extension-paragraph";

const ClearFloat = Paragraph.extend({
    addAttributes() {
        const parent = this.parent?.() ?? {};

        return {
            ...parent,
            clearFloat: {
                default: null,
                parseHTML: (element) =>
                    element.getAttribute("data-clear-float") || null,
                renderHTML: (attributes) => {
                    if (!attributes.clearFloat) {
                        return {};
                    }
                    return { "data-clear-float": "true" };
                },
            },
        };
    },

    addCommands() {
        return {
            ...this.parent?.(),
            toggleClearFloat:
                () =>
                ({ state, dispatch }) => {
                    const { $from } = state.selection;
                    const pos = $from.before($from.depth);
                    const node = state.doc.nodeAt(pos);

                    if (!node || node.type.name !== "paragraph") {
                        return false;
                    }

                    if (dispatch) {
                        const next = node.attrs.clearFloat ? null : true;
                        dispatch(
                            state.tr.setNodeMarkup(pos, undefined, {
                                ...node.attrs,
                                clearFloat: next,
                            }),
                        );
                    }

                    return true;
                },
        };
    },
});

export default ClearFloat;
