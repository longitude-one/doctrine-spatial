/**
 * Custom updater for `commit-and-tag-version` to update Installation.rst.
 *
 * The built-in `plain-text` type replaces the entire file with the version number,
 * which is unusable for Installation.rst. This updater replaces the version constraint
 * for longitude-one/doctrine-spatial in each installation example.
 */

const SEMVER_PATTERN = '\\d+\\.\\d+\\.\\d+(?:-[0-9A-Za-z.-]+)?(?:\\+[0-9A-Za-z.-]+)?';
const PACKAGE_VERSION_PATTERN = new RegExp(
  `(longitude-one\\/doctrine-spatial(?:":\\s*"|:)\\^)(${SEMVER_PATTERN})`,
);
const PACKAGE_VERSION_PATTERN_GLOBAL = new RegExp(
  `(longitude-one\\/doctrine-spatial(?:":\\s*"|:)\\^)${SEMVER_PATTERN}`,
  'g',
);

module.exports.readVersion = function (contents) {
  const match = contents.match(PACKAGE_VERSION_PATTERN);
  return match ? match[2] : '0.0.0';
};

module.exports.writeVersion = function (contents, version) {
  const updatedContents = contents.replace(
    PACKAGE_VERSION_PATTERN_GLOBAL,
    `$1${version}`,
  );

  if (updatedContents === contents) {
    throw new Error(
      'installation-version-updater: No Doctrine Spatial package version found in Installation.rst.',
    );
  }

  return updatedContents;
};
