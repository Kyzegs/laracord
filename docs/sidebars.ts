import type {SidebarsConfig} from '@docusaurus/plugin-content-docs';

// This runs in Node.js - Don't use client-side code here (browser APIs, JSX...)

/**
 * Creating a sidebar enables you to:
 - create an ordered group of docs
 - render a sidebar for each doc of that group
 - provide next/previous navigation

 The sidebars can be generated from the filesystem, or explicitly defined here.

 Create as many sidebars as you want.
 */
const sidebars: SidebarsConfig = {
  // By default, Docusaurus generates a sidebar from the docs folder structure
  tutorialSidebar: [
    {
      type: 'doc',
      id: 'getting-started',
      label: 'Getting Started',
    },
    {
      type: 'category',
      label: 'Installation',
      items: [
        'installation/installation',
        'installation/configuration',
      ],
    },
    {
      type: 'category',
      label: 'Usage',
      items: [
        'usage/authentication',
        'usage/making-requests',
        'usage/error-handling',
      ],
    },
    {
      type: 'category',
      label: 'API Reference',
      items: [
        'api',
        'api/application-commands',
        'api/channels',
        'api/guilds',
        'api/users',
        'api/webhooks',
      ],
    },
  ],
};

export default sidebars;
