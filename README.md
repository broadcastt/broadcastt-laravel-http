# Broadcastt

[![](https://img.shields.io/github/workflow/status/broadcastt/broadcastt-laravel-http/Test?style=flat-square)](https://github.com/broadcastt/broadcastt-laravel-http/actions?query=workflow%3ATest)
[![codecov](https://codecov.io/gh/broadcastt/broadcastt-laravel-http/branch/master/graph/badge.svg)](https://codecov.io/gh/broadcastt/broadcastt-laravel-http)
[![](https://img.shields.io/github/license/broadcastt/broadcastt-laravel-http?style=flat-square)](https://github.com/broadcastt/broadcastt-laravel-http/blob/master/LICENSE)
[![](https://img.shields.io/packagist/v/broadcastt/broadcastt-laravel-http?style=flat-square)](https://packagist.org/packages/broadcastt/broadcastt-laravel-http)

Realtime web applications are the future. [Broadcastt](https://broadcastt.xyz/) provides tools to help developers create realtime applications.

## Laravel HTTP Library

> Be aware that this library is still in beta and not reached the first MAJOR version.
> 
> Semantic Versioning 2.0.0
>
> Major version zero (0.y.z) is for initial development. Anything may change at any time. The public API should not be considered stable.

This library is compatible with Laravel 5.5+

This is an HTTP library for Laravel. If you are looking for a client library or a different server library please check out our [list of libraries](https://broadcastt.xyz/docs/libraries).

For tutorials and more in-depth documentation, visit the [official site](https://broadcastt.xyz/).

## Documentation

### First steps

Require this package, with [Composer](https://getcomposer.org/)

```
composer require broadcastt/broadcastt-laravel-http
```

The Broadcastt [service provider](http://laravel.com/docs/provider) is registered automatically.

```
Broadcastt\Laravel\BroadcasttServiceProvider
``` 

If you want you can register the Broadcastt [facade](http://laravel.com/docs/facades).

```
'Broadcastt' => Broadcastt\Laravel\Facades\Broadcastt::class
```

### Configuration

To configure only the [Broadcasting](https://laravel.com/docs/broadcasting) driver you have to modify `config/broadcasting.php`. You have to have an item in the `connections` array with its driver set to `broadcastt`.

```
'broadcastt' => [
	'driver' => 'broadcastt',
	'id' => env('BROADCASTER_APP_ID'),
	'key' => env('BROADCASTER_APP_KEY'),
	'secret' => env('BROADCASTER_APP_SECRET'),
	'cluster' => env('BROADCASTER_APP_CLUSTER'),
],
```

The recommended way is to use environment variables or use `.env` to configure these data.

You can also use the `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_ID` and `PUSHER_APP_CLUSTER` environment variables respectively.

To configure other connection for the facade you should publish `config/broadcastt.php`.

```
php artisan vendor:publish --provider="Broadcastt\Laravel\BroadcasttServiceProvider"
```

In the published `config/broadcasting.php` file you can define many connections.

#### `id` (Integer)

The id of the application

#### `key` (String)

The key of the application

#### `secret` (String)

The secret of the application

#### `cluster` (String) Optional

The cluster of the application

Default value: `eu`

### Additional options

#### `useTLS` (String)

Short way to change `scheme` to `https` and `port` to `443`

#### `debug` (Boolean)

Turns on debugging for all requests

Default value: `false`

#### `basePath` (String)

The base of the path what the request will call

Default value: `/apps/{AppId}`

#### `scheme` (String)

E.g. http or https

Default value: `http`

#### `host` (String)

The host e.g. cluster.broadcasttapp.com. No trailing forward slash

Default value: `eu.broadcasttapp.xyz` If the cluster is not set during initialization

#### `port` (String)

The http port

Default value: `80`

#### `timeout` (String)

The http timeout

Default value: `30`

### Usage of broadcaster

`BroadcasttServiceProvider` registers a driver for Broadcasting, so in `config/broadcasting.php` you can use `broadcastt` driver for broadcasting.

For example you can set `BROADCAST_DRIVER` environment variable to `broadcastt`.

### Usage of facade

#### `client($connection = null)`

Returns a client instance

#### `connection($connection = null)`

Alias for `client($connection = null)`

#### `trigger($channels, $name, $data, $socketId = null, $jsonEncoded = false)`

Trigger an event by providing event name and payload.

Optionally provide a socket ID to exclude a client (most likely the sender).

#### `triggerBatch($batch = [], $encoded = false)`

Trigger multiple events at the same time.

#### `get($path, $params = [])`

GET arbitrary REST API resource using a synchronous http client.

All request signing is handled automatically.

## Contributing

Everyone is welcome who would help to make this library "Harder, Better, Faster, Stronger".
