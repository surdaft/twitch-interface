<?php

use PHPUnit\Framework\TestCase;

use Twitch\BaseMethod;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class BaseMethodTest extends TestCase
{
    /**
     * @test
     */
    public function test_fetching_a_response()
    {
        $json_file = file_get_contents(__DIR__ . '/data/channel.json');
        $decoded_json_file = json_decode($json_file);

        $mock_handler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json'
            ], $json_file),
            new Response(200, [
                'Content-Type' => 'application/json'
            ], $json_file)
        ]);

        $client = new Client([
            'handler' => $mock_handler,
            'headers' => BaseMethod::getClientHeaders(),
            'base_uri' => 'https://api.twitch.tv/kraken/channels/surdaft'
        ]);

        $channel = (new BaseMethod($client))->send();
        $this->assertEquals($decoded_json_file, $channel);

        $channel = BaseMethod::fetch($client);
        $this->assertEquals($decoded_json_file, $channel);
    }

    public function test_fetching_a_channel_with_access_token()
    {

    }
}
