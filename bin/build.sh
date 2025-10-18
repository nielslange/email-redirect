#!/bin/bash

set -e

PLUGIN_SLUG="email-redirect"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="$PROJECT_ROOT/dist"
VERSION=$(grep -i "Version:" "$PROJECT_ROOT/$PLUGIN_SLUG.php" | awk -F':' '{print $2}' | tr -d '[:space:]')
TEMP_DIR="/tmp/$PLUGIN_SLUG"
ZIP_FILE="${PLUGIN_SLUG}-${VERSION}.zip"

rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR/$PLUGIN_SLUG" "$DIST_DIR"

# Copy entire project
cp -r "$PROJECT_ROOT"/* "$TEMP_DIR/$PLUGIN_SLUG/" 2>/dev/null || true

# Apply .distignore exclusions
if [ -f "$PROJECT_ROOT/.distignore" ]; then
    while IFS= read -r pattern; do
        [[ "$pattern" =~ ^#.*$ || -z "$pattern" ]] && continue
        pattern=$(echo "$pattern" | xargs)
        find "$TEMP_DIR/$PLUGIN_SLUG" -name "$pattern" -type f -delete 2>/dev/null || true
        find "$TEMP_DIR/$PLUGIN_SLUG" -name "$pattern" -type d -delete 2>/dev/null || true
    done < "$PROJECT_ROOT/.distignore"
fi

cd /tmp
rm -f "$ZIP_FILE"
zip -r -q "$ZIP_FILE" "$PLUGIN_SLUG"
mv "/tmp/$ZIP_FILE" "$DIST_DIR/$ZIP_FILE"
rm -rf "$TEMP_DIR"

echo "✅ Plugin bundle created: dist/$ZIP_FILE"
