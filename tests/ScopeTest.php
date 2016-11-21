<?php

use PHPUnit\Framework\TestCase;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

use Twitch\Scope;
use Twitch\Twitch;

class ScopeTest extends TestCase
{
    /**
     * @test
     */
    public function test_authorized_returns_array()
    {
       $scope = new Scope;
       $this->assertTrue(is_array($scope->authorized()));
    }
    
    /**
     * @test
     */
    public function test_adding_scopes()
    {
        $scope = new Scope;
        $scope->addScope('test');
        
        $this->assertEquals(['test'], $scope->authorized());
    }
    
    /**
     * @test
     */
    public function test_scope_is_authorized()
    {
        $scope = new Scope;
        
        $this->assertFalse($scope->isAuthorized('test'));
        
        $scope->addScope('test');
        
        $this->assertTrue($scope->isAuthorized('test'));
    }
    
    /**
     * @test
     */
    public function test_adding_array_of_scopes()
    {
        $scope = new Scope;
        $scope->addScope(['test1','test2']);
        
        $this->assertEquals(['test1','test2'], $scope->authorized());
    }
}
