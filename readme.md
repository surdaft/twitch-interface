This twitch interface is to allow an easy use of the Twitch API via PHP. There are many other composer libraries that accomplish this but they are not quite what I needed personally.

To use this Twitch interface all you need to do is go into your Twitch settings, head over to authorizations and at the bottom you can create your own app. Once created copy the `client_id` and put that somewhere for you to use within your code. I recommend a .env file so you can hide it in an environment variable and fetch it via short functions like `getenv('twitch.client_id');`.
Once you have your `client_id` just use the command:

```php
Twitch::setApiKey($client_id);
```

This defined a singleton existance of your client_id. This client_id is attached to all curl requests, it just means you will not be rate limited, which is nice.

To fetch a channel it is as easy as:

```php
$channel = Channel::fetch('surdaft');
```

There will be full documentation on each endpoint once development is complete, but here are a few more neat things.

- You are able to fetch your channel if you have `Twitch::setAccessToken($access_token);` defined, by just calling `Channel::fetch()`. This follows how the Twitch API works, if you were to attach an access_token to a request and just query channel.
