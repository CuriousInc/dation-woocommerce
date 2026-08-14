#!/usr/bin/env bash

# Assembles the distributable plugin payload into the directory given as $1.
#
# This is the single source of truth for what ships, used by both the `package`
# job (manual install ZIP) and `deploy-plugin.sh` (WordPress.org SVN). Note that
# `strauss` ships and `vendor` does not: dation-woocommerce.php loads
# strauss/autoload.php, which is the prefixed copy of our dependencies that
# `vendor/bin/strauss` generates during the build job. `vendor` only holds the
# unprefixed originals plus build/test tooling, none of which is needed at runtime.

set -euo pipefail

PLUGIN_BUILD_DIRECTORIES=(admin includes strauss contact-form)
PLUGIN_BUILD_FILES=(LICENSE dation-woocommerce.php readme.txt)

if [[ $# -ne 1 ]]; then
    echo "Usage: $0 <build-directory>" 1>&2
    exit 1
fi

BUILD_PATH="$1"

# Fail early with a clear message rather than shipping a plugin that fatals on
# load, which is what happens when strauss/ was never generated.
for DIRECTORY in "${PLUGIN_BUILD_DIRECTORIES[@]}"; do
    if [[ ! -d "$DIRECTORY" ]]; then
        echo "Required directory '$DIRECTORY' is missing. Did the build job run 'vendor/bin/strauss'?" 1>&2
        exit 1
    fi
done

if [[ ! -f strauss/autoload.php ]]; then
    echo "strauss/autoload.php is missing. Did the build job run 'vendor/bin/strauss'?" 1>&2
    exit 1
fi

rm -rf "$BUILD_PATH"
mkdir -p "$BUILD_PATH"

for DIRECTORY in "${PLUGIN_BUILD_DIRECTORIES[@]}"; do
    cp -r "$DIRECTORY" "$BUILD_PATH/$DIRECTORY"
done

for FILE in "${PLUGIN_BUILD_FILES[@]}"; do
    cp "$FILE" "$BUILD_PATH/$FILE"
done

echo "Assembled plugin payload in $BUILD_PATH"
