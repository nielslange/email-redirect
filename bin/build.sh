#!/bin/bash

set -e

PLUGIN_SLUG="email-redirect"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="$PROJECT_ROOT/dist"
VERSION=$(grep -i "Version:" "$PROJECT_ROOT/$PLUGIN_SLUG.php" | awk -F':' '{print $2}' | tr -d '[:space:]')
TEMP_DIR="/tmp/$PLUGIN_SLUG"
ZIP_FILE="${PLUGIN_SLUG}-${VERSION}.zip"

rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR" "$DIST_DIR"

# Build assets
echo "Building assets..."
cd "$PROJECT_ROOT"
pnpm run build

# Copy only the files we want to distribute
echo "Copying files to temp directory..."
cp "$PROJECT_ROOT/email-redirect.php" "$TEMP_DIR/"
cp -r "$PROJECT_ROOT/inc" "$TEMP_DIR/"
cp -r "$PROJECT_ROOT/assets" "$TEMP_DIR/"

# Remove source files and keep only minified
rm -rf "$TEMP_DIR/assets/scss"
rm -f "$TEMP_DIR/assets/js/admin.js"
rm -f "$TEMP_DIR/assets/css/*.map"
rm -f "$TEMP_DIR/assets/js/*.map"

cd /tmp
rm -f "$ZIP_FILE"
zip -r -q "$ZIP_FILE" "$PLUGIN_SLUG"
mv "/tmp/$ZIP_FILE" "$DIST_DIR/$ZIP_FILE"
rm -rf "$TEMP_DIR"

echo "✅ Plugin bundle created: dist/$ZIP_FILE"
