#!/bin/bash
# Replace all text domain occurrences in PHP files
PLUGIN_DIR="/opt/lampp/htdocs/test/wp-content/plugins/MHPlug"
OLD_DOMAIN="mh-plug"
NEW_DOMAIN="mh-plug-ecommerce-builder-widgets"

# Use a unique delimiter to avoid issues with slashes
find "$PLUGIN_DIR" -name "*.php" -not -path "*/.git/*" | while read -r file; do
    # Replace 'mh-plug' text domain usages in translation functions
    # Uses sed with a word-boundary-like pattern: matches 'mh-plug' only as a quoted string arg
    sed -i "s|'mh-plug'|'mh-plug-ecommerce-builder-widgets'|g" "$file"
done

echo "Done! All text domain references updated."
