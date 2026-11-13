const { Mark } = window.FilamentRichEditor.tiptap.core

const AffiliateLink = Mark.create({
    name: 'affiliateLink',

    inclusive() {
        return false
    },

    addAttributes() {
        return {
            affiliateLinkId: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-affiliate-id'),
                renderHTML: (attributes) => {
                    if (!attributes.affiliateLinkId) return {}
                    return { 'data-affiliate-id': attributes.affiliateLinkId }
                },
            },
        }
    },

    parseHTML() {
        return [{ tag: 'a[data-affiliate-id]' }]
    },

    renderHTML({ HTMLAttributes }) {
        return ['a', { class: 'affiliate-link', ...HTMLAttributes }, 0]
    },

    addCommands() {
        return {
            setAffiliateLink: (attributes) => ({ commands }) => {
                return commands.setMark(this.name, attributes)
            },
            unsetAffiliateLink: () => ({ commands }) => {
                return commands.unsetMark(this.name)
            },
        }
    },
})

export default AffiliateLink
