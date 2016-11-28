<?php

use Twitch\BaseMethod;

use Tests\TwitchInterfaceTestCase;

use GuzzleHttp\Psr7\Response;

class BaseMethodTest extends TwitchInterfaceTestCase
{
    /**
     * @test
     */
    public function test_default_verb()
    {
        $base_method = new BaseMethod($this->getClient());
        $this->assertEquals('GET', $base_method->verb());
    }

    /**
     * @test
     */
    public function test_recieving_expected_response_decoded()
    {
        $test_file = file_get_contents(__DIR__ . '/data/channel.json');

        $base_method = new BaseMethod($this->getClient([
            new Response(200, [], $test_file)
        ]));

        $this->assertEquals(json_decode($test_file), $base_method->send());
    }
}
