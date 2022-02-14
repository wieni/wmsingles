# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2022-01-20
Check [`UPGRADING.md`](UPGRADING.md) for instructions.

## Changed
- Rename module from `wmsingles` to `node_singles`
- Move module development to Drupal.org, changing the namespace from `wieni` to `drupal`

## [2.5.8] - 2022-01-20
## Changed
- Make README point to Drupal.org

## Fixed
- Fix upgrade command on macOS

## [2.5.7] - 2022-01-20
## Added
- Document 3.0.0 upgrade

## [2.5.6] - 2022-01-20
## Added
- Add PHP 8 support

## Fixed
- Increase Drupal core version due to path_alias dependency

## [2.5.5] - 2021-04-11
## Changed
- Disallow cloning singles using the [Entity Clone module](https://www.drupal.org/project/entity_clone)

## [2.5.4] - 2021-03-07
## Fixed
- Fix error in access control handler

## [2.5.3] - 2021-03-07
## Fixed
- Fix error in access control handler

## [2.5.2] - 2021-02-09
## Fixed
- Fix singles appearing on /node/add

## [2.5.1] - 2021-02-02
## Changed
- Replace operation alters with `SingleNodeAccessControlHandler`. This will cover more access handling. 
## Fixed
- Fix node & node type form alters not being executed

## [2.5.0] - 2020-12-22
## Added
- Add Twig extension

## [2.4.3] - 2020-11-27
## Removed
- Fix node add access check not always working

## [2.4.2] - 2020-11-10
## Removed
- Fix warning on singles overview when the user does not have edit access

## [2.4.1] - 2020-07-23
## Removed
- Remove call to path.alias_manager

## [2.4.0] - 2020-07-23
## Removed
- Remove hook_event_dispatcher dependency

## [2.3.2] - 2020-07-09
## Fixed
- Stop creating singles during cache rebuild. This feature made it in
 some cases impossible to remove single node types.

## [2.3.1] - 2020-06-23
## Changed
- Change overview labels to fallback to the node type label 

## [2.3.0] - 2020-04-22
## Changed
- Remove _Type_ column from overview

## [2.2.0] - 2020-03-12
### Added
- Add code style fixers
- Add `WmSinglesInterface`
- Add support for Drupal 9
- Add changelog

### Changed
- Change PHP dependency to 7.1
- Change private properties/methods to protected
- Add type hinting
- Update README
- Update .gitignore
- Update module description
- Fix code style
- Fix yaml indentation
- Remove unnecessary comments

### Removed
- Remove usage of ControllerBase
