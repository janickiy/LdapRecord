<?php

namespace LdapRecord\Tests;

use LdapRecord\Ldap;
use LdapRecord\Testing\LdapFake;
use Mockery as m;

class LdapTest extends TestCase
{
    public function test_construct_defaults()
    {
        $ldap = new Ldap();

        $this->assertFalse($ldap->isUsingTLS());
        $this->assertFalse($ldap->isUsingSSL());
        $this->assertFalse($ldap->isBound());
        $this->assertNull($ldap->getConnection());
    }

    public function test_host_arrays_are_properly_processed()
    {
        $ldap = new LdapFake;

        $ldap->connect(['dc01', 'dc02'], $port = 500);

        $this->assertEquals('ldap://dc01:500 ldap://dc02:500', $ldap->getHost());
    }

    public function test_host_strings_are_properly_processed()
    {
        $ldap = new LdapFake;

        $ldap->connect('dc01', $port = 500);

        $this->assertEquals('ldap://dc01:500', $ldap->getHost());
    }

    public function test_get_default_protocol()
    {
        $ldap = new Ldap();

        $this->assertEquals('ldap://', $ldap->getProtocol());
    }

    public function test_get_protocol_ssl()
    {
        $ldap = new Ldap();

        $ldap->ssl();

        $this->assertEquals('ldaps://', $ldap->getProtocol());
    }

    public function test_get_host()
    {
        $ldap = new Ldap();

        $ldap->connect('192.168.1.1');

        $this->assertEquals('ldap://192.168.1.1:389', $ldap->getHost());
    }

    public function test_get_host_is_null_without_connecting()
    {
        $ldap = new Ldap();

        $this->assertNull($ldap->getHost());
    }

    public function test_can_change_passwords()
    {
        $ldap = new Ldap();

        $ldap->ssl();

        $this->assertTrue($ldap->canChangePasswords());

        $ldap->ssl(false);

        $this->assertFalse($ldap->canChangePasswords());

        $ldap->tls();

        $this->assertTrue($ldap->canChangePasswords());
    }

    public function test_set_options()
    {
        $ldap = (new LdapFake)
            ->expect([
                LdapFake::operation('setOption')->once()->with(1, 'value'),
                LdapFake::operation('setOption')->once()->with(2, 'value'),
            ]);

        $ldap->setOptions([1 => 'value', 2 => 'value']);
    }

    public function test_get_detailed_error_returns_null_when_error_number_is_zero()
    {
        $ldap = m::mock(Ldap::class)->makePartial();

        $ldap->shouldReceive('errNo')->once()->andReturn(0);

        $this->assertNull($ldap->getDetailedError());
    }
}
