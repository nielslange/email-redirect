#!/bin/bash

set -e

PLUGIN_SLUG="email-redirect"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_FILE="$PROJECT_ROOT/$PLUGIN_SLUG.php"
PACKAGE_FILE="$PROJECT_ROOT/package.json"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get current version
CURRENT_VERSION=$(grep -i "Version:" "$PLUGIN_FILE" | awk -F':' '{print $2}' | tr -d '[:space:]')
echo -e "${YELLOW}Current version: $CURRENT_VERSION${NC}"

# Parse version parts (e.g., 1.2 -> major=1, minor=2)
IFS='.' read -r MAJOR MINOR <<< "$CURRENT_VERSION"

# Auto-bump patch version (1.0 -> 1.1 -> 1.2, etc.)
NEW_VERSION="$MAJOR.$((MINOR + 1))"
echo -e "${YELLOW}New version: $NEW_VERSION${NC}"

# Update version in plugin file
sed -i.bak "s/Version: $CURRENT_VERSION/Version: $NEW_VERSION/" "$PLUGIN_FILE"
rm "$PLUGIN_FILE.bak"

# Update version in package.json
sed -i.bak "s/\"version\": \"$CURRENT_VERSION\"/\"version\": \"$NEW_VERSION\"/" "$PACKAGE_FILE"
rm "$PACKAGE_FILE.bak"

echo -e "${GREEN}✅ Version updated to $NEW_VERSION${NC}"

# Build the zip
echo -e "${YELLOW}Building distribution zip...${NC}"
bash "$PROJECT_ROOT/bin/build.sh"

# Create git tag
echo -e "${YELLOW}Creating git tag v$NEW_VERSION...${NC}"
git add "$PLUGIN_FILE" "$PACKAGE_FILE"
git commit -m "Release v$NEW_VERSION"
git tag "v$NEW_VERSION"

echo -e "${GREEN}✅ Release v$NEW_VERSION created successfully!${NC}"
echo -e "${GREEN}📦 Distribution zip: dist/$PLUGIN_SLUG-$NEW_VERSION.zip${NC}"
echo -e "${YELLOW}💡 Don't forget to push: git push origin trunk --tags${NC}"
