# statsd-php

A PHP client library for the statistics daemon ([statsd](https://github.com/etsy/statsd)) intended to send metrics from PHP applications.

[![Build Status](https://github.com/slickdeals/statsd-php/workflows/Build%20statsd-php/badge.svg)](https://github.com/slickdeals/statsd-php/actions)

Originally a fork of https://github.com/domnikl/statsd-php and original author Dominik Liebler. The Slickdeals team has
taken over the project.

## Installation

The best way to install statsd-php is to use Composer and add the following to your project's `composer.json` file:

```javascript
{
    "require": {
        "slickdeals/statsd": "~3.0"
    }
}
```

## Usage

```php
<?php
$connection = new \Domnikl\Statsd\Connection\UdpSocket('localhost', 8125);
$statsd = new \Domnikl\Statsd\Client($connection, "test.namespace");

// the global namespace is prepended to every key (optional)
$statsd->setNamespace("test");

// simple counts
$statsd->increment("foo.bar");
$statsd->decrement("foo.bar");
$statsd->count("foo.bar", 1000);
```

When establishing the connection to statsd and sending metrics, errors will be suppressed to prevent your application from crashing.

If you run statsd in TCP mode, there is also a `\Domnikl\Statsd\Connection\TcpSocket` adapter that works like the `UdpSocket` except that it throws a `\Domnikl\Statsd\Connection\TcpSocketException` if no connection could be established.
Please consider that unlike UDP, TCP is used for reliable networks and therefor exceptions (and errors) will not be suppressed in TCP mode.

### [Timings](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#timing)

```php
<?php
// timings
$statsd->timing("foo.bar", 320);
$statsd->time("foo.bar.bla", function() {
    // code to be measured goes here ...
});

// more complex timings can be handled with startTiming() and endTiming()
$statsd->startTiming("foo.bar");
// more complex code here ...
$statsd->endTiming("foo.bar");
```

### Memory profiling

```php
<?php
// memory profiling
$statsd->startMemoryProfile('memory.foo');
// some complex code goes here ...
$statsd->endMemoryProfile('memory.foo');

// report peak usage
$statsd->memory('foo.memory_peak_usage');
```

### [Gauges](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#gauges)

statsd supports gauges, arbitrary values which can be recorded. 

This method accepts both absolute (3) and delta (+11) values. 

*NOTE:* Negative values are treated as delta values, not absolute.

```php
<?php
// Absolute value
$statsd->gauge('foobar', 3);

// Pass delta values as a string. 
// Accepts both positive (+11) and negative (-4) delta values.
$statsd->gauge('foobar', '+11'); 
```

### [Sets](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#sets)

statsd supports sets, so you can view the uniqueness of a given value.

```php
<?php
$statsd->set('userId', 1234);
```

### disabling sending of metrics

To disable sending any metrics to the statsd server, you can use the `Domnikl\Statsd\Connection\Blackhole` connection
 class instead of the default socket abstraction. This may be incredibly useful for feature flags. Another options is
to use `Domnikl\Statsd\Connection\InMemory` connection class, that will collect your messages but won't actually send them.

## StatsdAwareInterface

You can use the `StatsdAwareInterface` and `StatsdAwareTrait` in order to have dependency injection containers (such as
Symfony's DI component) automatically detect the StatsdAwareInterface and inject the client into your service.

### Symfony

```yaml
# config/services.yaml
services:
  _instanceof:
    Domnikl\Statsd\StatsdAwareInterface:
      calls:
        - [setStatsdClient, ['@Domnikl\Statsd\Client']]

  Domnikl\Statsd\Client:
    arguments:
      $connection: '@app.statsd_connection'
      $namespace: '<namespace>'

  app.statsd_connection:
    class: Domnikl\Statsd\Connection\UdpSocket
    arguments:
      $host: '%env(STATSD_HOST)%'
      $port: '%env(STATSD_PORT)%'
```

## Authors

Original author: Dominik Liebler <liebler.dominik@gmail.com>
Several other [contributors](https://github.com/slickdeals/statsd-php/graphs/contributors) - Thank you!
