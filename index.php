<pre>
<?php

include 'vendor/autoload.php';

use Dotenv\Dotenv;

use Twitch\Twitch;
use Twitch\Channel;

$dotenv = (new Dotenv(__DIR__))->load();

Twitch::setApiKey(getenv('twitch.client_id'));
Twitch::setAccessToken(getenv('twitch.access_token'));

$channel = Channel::fetch('hackslashdave');

print_r($channel);
