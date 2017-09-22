# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
