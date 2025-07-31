# Laracord Documentation

This directory contains the documentation for Laracord, built with Docusaurus.

## Quick Start

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Generate API documentation:**
   ```bash
   php generate-api-docs.php
   ```

3. **Start the development server:**
   ```bash
   npm start
   ```

4. **Build for production:**
   ```bash
   npm run build
   ```

## Structure

```
docs/
├── docs/                    # Documentation pages
│   ├── getting-started.md
│   ├── installation/
│   ├── usage/
│   └── api/
├── src/
│   ├── css/                # Custom styles
│   └── pages/              # React pages
├── static/                 # Static assets
├── docusaurus.config.ts    # Docusaurus configuration
├── sidebars.ts            # Navigation sidebar
├── generate-api-docs.php  # API documentation generator
└── package.json           # Node.js dependencies
```

## API Documentation Generation

The `generate-api-docs.php` script parses the Laracord Facade and generates comprehensive API documentation. It:

1. Extracts all `@method` annotations from the facade
2. Parses method signatures and parameters
3. Categorizes methods by functionality
4. Generates markdown documentation with examples
5. Creates parameter tables with types and requirements

### Running the Generator

```bash
cd docs
php generate-api-docs.php
```

This will generate:
- `docs/api.md` - Main API reference page
- `docs/api/` - Category-specific API pages

## Customization

### Adding New Documentation Pages

1. Create a new markdown file in the appropriate directory
2. Add it to the sidebar configuration in `sidebars.ts`
3. Follow the existing documentation style

### Styling

- Global styles: `src/css/custom.css`
- Component-specific styles: Create CSS modules in `src/pages/`

### Configuration

- Main config: `docusaurus.config.ts`
- Navigation: `sidebars.ts`
- Theme customization: `src/css/custom.css`

## Deployment

### GitHub Pages

1. Build the documentation:
   ```bash
   npm run build
   ```

2. Deploy to GitHub Pages:
   ```bash
   npm run deploy
   ```

### Netlify/Vercel

1. Connect your repository
2. Set build command: `npm run build`
3. Set publish directory: `build`

### Custom Domain

1. Add your domain to the `docusaurus.config.ts`
2. Configure DNS settings
3. Update the `url` and `baseUrl` in the config

## Development

### Adding New Features

1. **New React components:** Add to `src/pages/`
2. **New documentation:** Add to `docs/`
3. **Styling:** Add to `src/css/` or create CSS modules

### Testing

```bash
# Type checking
npm run typecheck

# Build testing
npm run build
```

## Contributing

1. Make your changes
2. Test locally with `npm start`
3. Build to ensure no errors: `npm run build`
4. Submit a pull request

## Troubleshooting

### Common Issues

**"Module not found" errors:**
- Run `npm install` to install dependencies
- Clear node_modules and reinstall if needed

**Build errors:**
- Check for syntax errors in markdown files
- Ensure all referenced files exist
- Verify sidebar configuration

**API generation errors:**
- Ensure the Laracord facade path is correct
- Check PHP version compatibility
- Verify autoloader is working

### Getting Help

- Check the [Docusaurus documentation](https://docusaurus.io/)
- Review existing documentation for patterns
- Open an issue for bugs or feature requests
