# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.2.2]
### Changed
- Testing newer versions of PHP 8
- Set $memory as nullable

## [3.2.1]
### Changed
- Set sample rate to be minimum of global and metric sample rate

## [3.2.0]
### Fixed
- Use tags to allow nested time() calls.

## [3.1.2]
### Fixed
- Updated dependency to replace all legacy versions in domnikl/statsd.

## [3.1.1] - 2022-08-09
### Fixed
- Fix mismatch between StatsdAwareTrait and StatsdAwareInterface which breaks php 7.4 compatibility.

## [3.1.0] - 2021-06-04
### Added
- Adds utility classes for helping to auto-inject the StatsD client into a service using a dependency injection container (like Symfony's DI component).

## [3.0.1] - 2019-10-19
### Fixed
- fixed #67 UDPSocket handle DNS change

## [3.0.0] - 2019-09-01
### Added
- PHP 7.2 is the new minimum PHP version
- added type hints and return type declarations

## [2.2.0] - 2015-10-29
### Added
- TcpSocket now throws a TCPSocketException if no connection could be established

## [2.0.0] 2014-12-09
### Changed
- renamed Socket classes: Socket is now a UdpSocket + there is a new TcpSocket class
- batch messages are split to fit into the configured MTU
- sampling all metrics must now be configured on the client - no longer in the connection
- endTiming() returns the time measured
- for development there is a new (simple) process for running integration tests and such using make

## [1.1.0] - 2014-08-16
### Added
- added support for [sets](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#sets)
- added support for [gauges](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#gauges)
- support batch-sending of metrics
- support sampling of metrics

## [1.0.2] - 2014-01-21
### Changed
- ignore errors when writing on the UDP sockets

## [1.0.1] - 2013-08-16
### Fixed
- ignore all exceptions and errors which are thrown when writing to the UDP socket

## [1.0.0] - 2013-01-25
### Added
- first version supporting counters, timings

[Unreleased]: https://github.com/Slickdeals/statsd-php/compare/3.2.0...HEAD
[3.2.1]: https://github.com/Slickdeals/statsd-php/compare/3.2.0...3.2.1
[3.2.0]: https://github.com/Slickdeals/statsd-php/compare/3.1.2...3.2.0
[3.1.2]: https://github.com/Slickdeals/statsd-php/compare/3.1.1...3.1.2
[3.1.1]: https://github.com/Slickdeals/statsd-php/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/Slickdeals/statsd-php/compare/3.0.1...3.1.0
[3.0.1]: https://github.com/Slickdeals/statsd-php/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/Slickdeals/statsd-php/compare/2.2.0...3.0.0
[2.2.0]: https://github.com/Slickdeals/statsd-php/compare/2.0.0...2.2.0
[2.0.0]: https://github.com/Slickdeals/statsd-php/compare/1.1.0...2.0.0
[1.1.0]: https://github.com/Slickdeals/statsd-php/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/Slickdeals/statsd-php/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/Slickdeals/statsd-php/compare/1.0.0...1.0.1
[1.0.1]: https://github.com/Slickdeals/statsd-php/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/Slickdeals/statsd-php/releases/tag/1.0.0
