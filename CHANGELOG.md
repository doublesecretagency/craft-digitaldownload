# Changelog

## 2.1.8 - 2021-10-15

### Changed
- Parse environment variables in Volume paths. (thanks @Peter-The-Great)

## 2.1.7 - 2020-08-19

### Changed
- Craft 3.5 is now required.

### Fixed
- Adjusted raw HTML output on settings page.

## 2.1.6 - 2020-08-07

### Changed
- Redirect anonymous downloads to login page when a user is required, instead of just showing an error message.

## 2.1.5 - 2019-10-16

### Fixed
- Fixed a bug where `flush` may trigger an error.

## 2.1.4 - 2019-09-16

### Changed
- Improved timing of download tracking mechanism.

## 2.1.3 - 2019-05-24

### Added
- Added error message for a missing file, assuming token & asset are otherwise correct.

### Fixed
- Normalize local filepaths.

## 2.1.2 - 2019-05-23

### Fixed
- Fixed bug which prevented downloading cloud assets that contained spaces in the filename.

## 2.1.1 - 2019-05-21

### Fixed
- Removed unnecessary parameter in link methods.

## 2.1.0 - 2019-05-21

### Added
- Can now download files from volumes without public URLs.
- Allows optional HTTP headers to be included at download.
- Added [`getLinkData`](https://www.doublesecretagency.com/plugins/digital-download/docs/get-link-data-from-a-token) variable to see what a token represents.

### Changed
- Significantly improved performance for large file downloads.
- Improved messages for invalid links.

### Fixed
- Fixed bug which allowed expiration timestamps to be slightly inaccurate.
- Fixed uninstall bug.

## 2.0.0 - 2018-08-13

### Added
- Craft 3 compatibility.

## 1.1.0 - 2017-10-02

### Added
- Added short [path download](https://www.doublesecretagency.com/plugins/digital-download/docs/short-download-links) URLs.

### Fixed
- Fixed PHP 7 race condition.
- Fixed null array bug.

## 1.0.0 - 2016-03-01

Initial release.
