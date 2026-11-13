const { Mark } = window.FilamentRichEditor.tiptap.core

const InternalLink = Mark.create({
    name: 'internalLink',

    inclusive() {
        return false
    },

    addAttributes() {
        return {
            postId: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-post-id'),
                renderHTML: (attributes) => {
                    if (!attributes.postId) return {}
                    return { 'data-post-id': attributes.postId }
                },
            },
            withArrow: {
                default: false,
                parseHTML: (element) => element.hasAttribute('data-with-arrow'),
                renderHTML: (attributes) => {
                    if (!attributes.withArrow) return {}
                    return { 'data-with-arrow': '' }
                },
            },
        }
    },

    parseHTML() {
        return [{ tag: 'a[data-post-id]' }]
    },

    renderHTML({ HTMLAttributes }) {
        return ['a', { class: 'internal-link', ...HTMLAttributes }, 0]
    },

    addCommands() {
        return {
            setInternalLink: (attributes) => ({ commands }) => {
                return commands.setMark(this.name, attributes)
            },
            unsetInternalLink: () => ({ commands }) => {
                return commands.unsetMark(this.name)
            },
        }
    },
})

export default InternalLink
