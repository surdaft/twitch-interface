<?php

use PHPUnit\Framework\TestCase;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

use Twitch\Scope;
use Twitch\Twitch;

class ScopeTest extends TestCase
{
    private function setTokens()
    {
        Twitch::setClientId();
        Twitch::setAccessToken();
    }

    /**
     * @test
     */
    public function get_currently_authorized_scopes()
    {
        $scopes = Scope::authorized();
        $this->assertEquals([], $scopes);
    }

    /**
     * @test
     */
    public function insert_authorized_scope()
    {
        Scope::addAuthorized('test_scope');
        Scope::addAuthorized(['test_scope1', 'test_scope2']);

        $this->assertEquals([
            'test_scope',
            'test_scope1',
            'test_scope2'
        ], Scope::authorized());
    }

    /**
     * @test
     */
    public function is_scope_authorized()
    {
        Scope::addAuthorized('test');
        $active = Scope::isAuthorized('test');
        $inactive = Scope::isAuthorized('not_test');

        $this->assertTrue($active);
        $this->assertFalse($inactive);
    }

    /**
     * @test
     */
    public function remove_authorized_scopes()
    {
        // does not exist
        $this->assertTrue(Scope::removeAuthorized('not_gonna_find_me'));
        // an array of scopes
        $this->assertTrue(Scope::removeAuthorized(['test_scope', 'test_scope1', 'test_scope2']));
        // single scope
        $this->assertTrue(Scope::removeAuthorized('test'));

        $this->assertEquals([], Scope::authorized());
    }

    /**
     * @test
     */
    public function set_access_token_scopes()
    {
        $this->setTokens();

        $this->assertEquals([
            'user_read'
        ], Scope::authorized());
    }

    /**
     * @test
     */
    public function scopes_are_wiped_with_new_token()
    {
        Scope::resetScopes();
        Scope::addAuthorized('test');

        $this->setTokens();

        $this->assertTrue(!in_array('test', Scope::authorized()));
    }
}
