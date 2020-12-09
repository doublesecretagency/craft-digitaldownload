module.exports = {
    markdown: {
        anchor: { level: [2, 3] },
        extendMarkdown(md) {
            let markup = require('vuepress-theme-craftdocs/markup');
            md.use(markup);
        },
    },
    base: '/digital-download/',
    title: 'Digital Download plugin for Craft CMS',
    plugins: [
        [
            'vuepress-plugin-clean-urls',
            {
                normalSuffix: '/',
                indexSuffix: '/',
                notFoundPath: '/404.html',
            },
        ],
    ],
    theme: 'craftdocs',
    themeConfig: {
        codeLanguages: {
            php: 'PHP',
            twig: 'Twig',
            js: 'JavaScript',
        },
        logo: '/images/icon.svg',
        searchMaxSuggestions: 10,
        nav: [
            {text: 'Getting Started️', link: '/getting-started/'},
            {
                text: 'How It Works',
                items: [
                    {text: 'Creating a Token', link: '/creating-a-token/'},
                    {text: 'Displaying a Link', link: '/displaying-a-link/'},
                    {text: 'Storing a Token', link: '/storing-a-token/'},
                    {text: 'Short Download Links', link: '/short-download-links/'},
                    {text: 'Get Link Data from a Token', link: '/get-link-data-from-a-token/'},
                ]
            },
            {
                text: 'More',
                items: [
                    {text: 'Double Secret Agency', link: 'https://www.doublesecretagency.com/plugins'},
                    {text: 'Our other Craft plugins', link: 'https://plugins.doublesecretagency.com', target:'_self'},
                ]
            },
        ],
        sidebar: {
            '/': [
                'getting-started',
                'creating-a-token',
                'displaying-a-link',
                'storing-a-token',
                'short-download-links',
                'get-link-data-from-a-token',
            ],
        }
    }
};
