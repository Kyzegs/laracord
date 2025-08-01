import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

// Load environment variables
dotenv.config();

// Get the site URL from environment or use default
const siteUrl = process.env.SITE_URL!;

// Get __dirname equivalent for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Define the config interface
interface ScraperConfig {
    index_name: string;
    start_urls: string[];
    sitemap_urls: string[];
    sitemap_alternate_links: boolean;
    stop_urls: string[];
    selectors: {
        lvl0: {
            selector: string;
            type: string;
            global: boolean;
            default_value: string;
        };
        lvl1: string;
        lvl2: string;
        lvl3: string;
        lvl4: string;
        lvl5: string;
        lvl6: string;
        text: string;
    };
    strip_chars: string;
    custom_settings: {
        separatorsToIndex: string;
        attributesForFaceting: string[];
        attributesToRetrieve: string[];
    };
    conversation_id: string[];
    nb_hits: number;
}

// Generate the config.json content
const config: ScraperConfig = {
    index_name: process.env.TYPESENSE_COLLECTION_NAME!,
    start_urls: [
        `${siteUrl}/`
    ],
    sitemap_urls: [
        `${siteUrl}/sitemap.xml`
    ],
    sitemap_alternate_links: true,
    stop_urls: [
        "/tests"
    ],
    selectors: {
        lvl0: {
            selector: "(//ul[contains(@class,'menu__list')]//a[contains(@class, 'menu__link menu__link--sublist menu__link--active')]/text() | //nav[contains(@class, 'navbar')]//a[contains(@class, 'navbar__link--active')]/text())[last()]",
            type: "xpath",
            global: true,
            default_value: "Documentation"
        },
        lvl1: "header h1",
        lvl2: "article h2",
        lvl3: "article h3",
        lvl4: "article h4",
        lvl5: "article h5, article td:first-child",
        lvl6: "article h6",
        text: "article p, article li, article td:last-child"
    },
    strip_chars: " .,;:#",
    custom_settings: {
        separatorsToIndex: "_",
        attributesForFaceting: [
            "language",
            "version",
            "type",
            "docusaurus_tag"
        ],
        attributesToRetrieve: [
            "hierarchy",
            "content",
            "anchor",
            "url",
            "url_without_anchor",
            "type"
        ]
    },
    conversation_id: [
        process.env.SCRAPER_CONVERSATION_ID!
    ],
    nb_hits: parseInt(process.env.SCRAPER_NB_HITS!)
};

// Write the config to config.json
const configPath = path.join(__dirname, 'config.json');
fs.writeFileSync(configPath, JSON.stringify(config, null, 4));

console.log(`Generated config.json with site URL: ${siteUrl}`);
console.log(`Collection name: ${config.index_name}`);
