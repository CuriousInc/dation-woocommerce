
PLUGIN_BUILD_DIRECTORIES=(admin includes vendor)
PLUGIN_BUILD_FILES=(LICENSE dation-woocommerce.php readme.txt)
PLUGIN_BUILD_PATH="/tmp/build"
PLUGIN_SVN_PATH="/tmp/svn"

LATEST_SVN_TAG="v1.0.4"

# Create the build directory
mkdir $PLUGIN_BUILD_PATH

# Copy plugin directories to the build directory
for DIRECTORY in "${PLUGIN_BUILD_DIRECTORIES[@]}"; do
    cp -r $DIRECTORY $PLUGIN_BUILD_PATH/$DIRECTORY
done

# Copy plugin files to the build directory
for FILE in "${PLUGIN_BUILD_FILES[@]}"; do
    cp $FILE $PLUGIN_BUILD_PATH/$FILE
done

# Checkout the SVN repo
svn co -q "http://svn.wp-plugins.org/$WP_ORG_PLUGIN_NAME" $PLUGIN_SVN_PATH

# Move to SVN directory
cd $PLUGIN_SVN_PATH

# Delete the trunk directory
rm -rf ./trunk

# Copy our new version of the plugin as the new trunk directory
cp -r $PLUGIN_BUILD_PATH ./trunk

# Copy our new version of the plugin into new version tag directory
cp -r $PLUGIN_BUILD_PATH ./tags/$LATEST_SVN_TAG

# Add new files to SVN
svn stat | grep '^?' | awk '{print $2}' | xargs -I x svn add x@

# Remove deleted files from SVN
svn stat | grep '^!' | awk '{print $2}' | xargs -I x svn rm --force x@

# Commit to SVN
svn ci --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD -m "Deploy version $LATEST_SVN_TAG"
