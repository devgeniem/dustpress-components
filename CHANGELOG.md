# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

## [1.2.3] - 2018-03-21

### Changed
- Changed static component group field to not be required

## [1.2.2] - 2018-03-05

### Changed
- Fix empty flexible fields

## [1.2.1] - 2018-03-05

### Changed
- Fixed normal components not running component data function

## [1.2.0] - 2018-03-05

### Changed
- Fixed static components not running component data function

## [1.1.4] - 2018-02-27

### Changed
- Fix component plugins with different file names than plugin.php

## [1.1.3] - 2018-01-26

### Fixed
- Fixed a bug where non-Codifier component with options would cause an error

## [1.1.2]

### Fixed

- Added ACF Codifier example for adding a rule group to the field group.
- Fixed plugin.php version number.

## [1.1.1]

### Fixed

- Documentation fixes.

## [1.1.0]

### Added

- A functionality to disable data filtering with a PHP constant.

## [1.0.0] - 2017-11-22

### Added
- Added lots of filters to allow modifying various settings

## [0.7.2] - 2017-11-20

### Changed
- Changed ACF Codifier's Composer require to include newer version

## [0.7.1] - 2017-11-17

### Changed
- Changed ACF Codifier's Composer require to point to the stable release

## [0.7.0] - 2017-11-16

### Changed
- Changed the component data filter to be more logically named
- Fixed the before method that wasn't ran in several previous versions
- Changed ACF Codifier's require block in the composer.json to one that's working

## [0.6.2] - 2017-10-30

### Added
- Added ACF Codifier as a dependency for the plugin
- Made component's data function to be overridable
- Added documention for overriding data function

### Changed
- Changed all built-in components to be defined with ACF Codifier
- Changed documentation to reflect the Codifier-related changes

## [0.6.0] - 2017-10-25

### Added
- Support for ACF Codifier components and option fields

## [0.5.2] - 2017-09-22

### Changed
- Moved Components::hook function to load on init instead of acf/init so we can use data that is set after init in fields

## [0.5.1] - 2017-09-22

### Added
- Static components now have acf_fc_layout field as well

### Changed
- Static components don't have a prefix anymore as this would break component data functions
- Changed clonable static component name from c to component name


## [0.5.0] - 2017-09-08

### Changed
- WPTEAM-59: Changed component_handle function to handle components more flexibly by searching for components in fields rather than component group fields
