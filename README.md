# Broadcastt

Realtime web applications are the future. [Broadcastt](https://broadcastt.xyz/) provides tools to help developers create realtime applications.

## Laravel HTTP Library

> Be aware that this library is still in beta and not reached the first MAJOR version.
> 
> Semantic Versioning 2.0.0
>
> Major version zero (0.y.z) is for initial development. Anything may change at any time. The public API should not be considered stable.

This library is compatible with Laravel 5.x

This is an HTTP library for Laravel. If you are looking for a client library or a different server library please check out our [list of libraries](https://broadcastt.xyz/docs/libraries).

For tutorials and more in-depth documentation, visit our [official site](https://broadcastt.xyz/).

## Documentation

### First steps

Require this package, with [Composer](https://getcomposer.org/)

```
composer require nergal/broadcastt-laravel-http
```

Since Laravel 5.5 our [service provider](http://laravel.com/docs/provider) is registered automatically. If you have an earlier version you have to register it. [Click here](https://laravel.com/docs/5.0/providers#registering-providers) if you don't know how to register a service provider.

```
Broadcastt\Laravel\BroadcasttServiceProvider
``` 

If you want you can register our [facade](http://laravel.com/docs/facades).

```
'Broadcastt' => Broadcastt\Laravel\Facades\Broadcastt::class
```

### Configuration

To configure only the [Broadcasting](https://laravel.com/docs/broadcasting) driver and the default connection for the facade you have to modify `config/broadcasting.php` as you have to ad an item to the `connections` array.

```
'broadcastt' => [
	'driver' => 'broadcastt',
	'id' => env('BROADCASTER_APP_ID'),
	'key' => env('BROADCASTER_APP_KEY'),
	'secret' => env('BROADCASTER_APP_SECRET'),
	'cluster' => env('BROADCASTER_APP_CLUSTER'),
],
```

We recommend to use your `.env` to configure these data.

You can also use the `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_ID` and `PUSHER_APP_CLUSTER` environment variables respectively.

To configure other connection for the facade you should publish `config/broadcastt.php`.

```
php artisan vendor:publish --provider="Broadcastt\Laravel\BroadcasttServiceProvider"
```

Here you can define a connection with the name `default` and override the one in `config/broadcasting.php`.

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

#### `encrypted` (String)

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

#### `curlOptions` (Mixed[])

Options for the curl instance

Default value: `[]`

### Usage of broadcaster

`BroadcasttServiceProvider` registers a driver for Broadcasting, so if you set the default configuration in `config/broadcasting.php` you can use `broadcastt` driver for broadcasting.

For example you can set `BROADCAST_DRIVER` environment variable to `broadcastt`.

### Usage of facade

#### `on($connection = null)`

Returns a connection instance

#### `event($channels, $name, $data, $socketId = null, $jsonEncoded = false)`

Trigger an event by providing event name and payload.

Optionally provide a socket ID to exclude a client (most likely the sender).

#### `eventBatch($batch = [], $encoded = false)`

Trigger multiple events at the same time.

#### `get($path, $params = [])`

GET arbitrary REST API resource using a synchronous http client.

All request signing is handled automatically.

## Contributing

We welcome everyone who would help us to make this library "Harder, Better, Faster, Stronger".
