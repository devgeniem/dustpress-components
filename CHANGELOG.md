# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Moved Components::hook function to load on init instead of acf/init so we can use data that is set after init in fields

## [0.5.0] - 2017-09-08

### Changed
- WPTEAM-59: Changed component_handle function to handle components more flexibly by searching for components in fields rather than component group fields
