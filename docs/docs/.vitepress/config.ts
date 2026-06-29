import { defineConfig } from 'vitepress'

// Scaffolded with `npx vitepress init`; customized for Laracord.
export default defineConfig({
  title: 'Laracord',
  description: 'Laravel client for Discord HTTP API',
  cleanUrls: true,
  lastUpdated: true,
  vite: {
    publicDir: 'static',
  },
  sitemap: {
    hostname: process.env.SITE_URL ?? 'https://docs.laracord.kyzegs.com',
  },
  head: [
    ['link', { rel: 'icon', type: 'image/x-icon', href: '/img/favicon.ico' }],
    ['link', { rel: 'icon', type: 'image/svg+xml', href: '/img/logo-badge.svg' }],
    ['meta', { name: 'theme-color', content: '#5865f2' }],
  ],
  themeConfig: {
    logo: '/img/logo.svg',
    nav: [
      { text: 'Guide', link: '/getting-started' },
      { text: 'API', link: '/api' },
      { text: 'Endpoints', link: '/api/endpoints' },
    ],
    sidebar: [
      {
        text: 'Start',
        items: [
          { text: 'Overview', link: '/' },
          { text: 'Getting Started', link: '/getting-started' },
          { text: 'Installation', link: '/installation/installation' },
          { text: 'Configuration', link: '/installation/configuration' },
        ],
      },
      {
        text: 'Usage',
        items: [
          { text: 'Authentication', link: '/usage/authentication' },
          { text: 'Making Requests', link: '/usage/making-requests' },
          { text: 'Error Handling', link: '/usage/error-handling' },
          { text: 'Rate Limits', link: '/usage/rate-limits' },
          { text: 'Notifications', link: '/usage/notifications' },
          { text: 'Interactions', link: '/usage/interactions' },
          { text: 'Components', link: '/usage/components' },
          { text: 'Testing', link: '/usage/testing' },
          { text: 'Migration from 0.x', link: '/usage/migration-v1' },
        ],
      },
      {
        text: 'API Reference',
        items: [
          { text: 'Client API', link: '/api' },
          { text: 'Endpoint Catalog', link: '/api/endpoints' },
        ],
      },
    ],
    search: {
      provider: 'local',
      options: {
        miniSearch: {
          searchOptions: {
            fuzzy: 0.2,
            prefix: true,
            boost: { title: 4, text: 2, titles: 1 },
          },
        },
      },
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/Kyzegs/laracord' },
    ],
    editLink: {
      pattern: 'https://github.com/Kyzegs/laracord/edit/develop/docs/docs/:path',
      text: 'Edit this page on GitHub',
    },
    footer: {
      message: 'Released under the MIT License.',
      copyright: `Copyright © ${new Date().getFullYear()} Laracord`,
    },
  },
})
