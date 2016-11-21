[![Build Status](https://travis-ci.org/surdaft/twitch-interface.svg?branch=master)](https://travis-ci.org/surdaft/twitch-interface)

This twitch interface is to allow an easy use of the Twitch API via PHP. There are many other composer libraries that accomplish this but they are not quite what I needed personally.

Most methods are replicated in a similar structure from the [Twitch API](https://dev.twitch.tv), though some may be missing or executed in a way you would not expect.
If this is the case then please leave an issue here and I or another developer can look at implementing it. If you would like to do a pull request then that is more than welcome too.

# How to use
To use the interface you need to set your `Client ID`, and depending on what endpoint your `Access Token` too.
```php
\Twitch\Twitch::setClientId('client_id');
\Twitch\Twitch::setAccessToken('access_token');
```

 > When using `Twitch::setAccessToken()` an API call is made to set your `\Twitch\Twitch::$scope`. If you `print_r(\Twitch\Twitch::$scope->authorized())` you can see all the authorized scopes for that access token. Allowing you to determine early whether someone is authorized to do an action or not.

You are now ready to fetch a channel.
```php
\Twitch\Channel::fetch('surdaft')->send();
```

If however you would like to use a channel but not get it straight away you can use `\Twitch\Channel::fetch('surdaft');` and then work on it like so:
```php
use Twitch\Channel;

$channel = Channel::fetch('surdaft');

// update the game and status
$channel->game('Overwatch');
$channel->status('Onlywatch Memes');

// now we are ready to send the API call
$channel->send();
```

All methods require `send()` at the end, this is to trigger the API call.

## Minor notes

A scope does not exist before the access token is added. If you would like to work with and without the access token but your system works around the availability of a scope, then add this in your code when it initializes.
```php
\Twitch\Twitch::$scope = new \Twitch\Scope;
```