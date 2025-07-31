#!/bin/bash

# Laracord Documentation Setup Script

echo "🚀 Setting up Laracord Documentation..."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18 or higher."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.1 or higher."
    exit 1
fi

echo "✅ Prerequisites check passed"

# Install npm dependencies
echo "📦 Installing npm dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install npm dependencies"
    exit 1
fi

echo "✅ npm dependencies installed"

# Generate API documentation
echo "📚 Generating API documentation..."
php generate-api-docs.php

if [ $? -ne 0 ]; then
    echo "❌ Failed to generate API documentation"
    exit 1
fi

echo "✅ API documentation generated"

# Create static assets directory if it doesn't exist
mkdir -p static/img

# Create a simple logo placeholder
echo "🎨 Creating placeholder logo..."
cat > static/img/logo.svg << 'EOF'
<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="32" height="32" rx="8" fill="#5865f2"/>
  <path d="M8 12h16v8H8z" fill="white"/>
  <circle cx="12" cy="16" r="2" fill="#5865f2"/>
  <circle cx="20" cy="16" r="2" fill="#5865f2"/>
</svg>
EOF

echo "✅ Setup complete!"

echo ""
echo "🎉 Laracord documentation is ready!"
echo ""
echo "Next steps:"
echo "1. Start the development server: npm start"
echo "2. Open http://localhost:3000 in your browser"
echo "3. Edit documentation files in the docs/ directory"
echo "4. Regenerate API docs when needed: php generate-api-docs.php"
echo ""
echo "For more information, see README.md"
