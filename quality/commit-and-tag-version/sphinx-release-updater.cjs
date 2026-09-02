/**
 * Custom updater for `commit-and-tag-version` to update docs/conf.py.
 */

const SEMVER_PATTERN = '\\d+\\.\\d+\\.\\d+(?:-[0-9A-Za-z.-]+)?(?:\\+[0-9A-Za-z.-]+)?';
const RELEASE_PATTERN = new RegExp(
  `^(\\s*release\\s*=\\s*['"])(${SEMVER_PATTERN})(['"].*)$`,
  'm',
);

module.exports.readVersion = function (contents) {
  const match = contents.match(RELEASE_PATTERN);
  return match ? match[2] : '0.0.0';
};

module.exports.writeVersion = function (contents, version) {
  const updatedContents = contents.replace(
    RELEASE_PATTERN,
    `$1${version}$3`,
  );

  if (updatedContents === contents) {
    throw new Error(
      'sphinx-release-updater: No Sphinx release value found in docs/conf.py.',
    );
  }

  return updatedContents;
};
