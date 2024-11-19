<?php

namespace Psecio\Gatekeeper\Session;

use Psecio\Gatekeeper\Base;
use Psecio\Gatekeeper\UserModel;
use Psecio\Gatekeeper\AuthTokenModel;
use Psecio\Gatekeeper\Session\RememberMe;

class RememberMeTest extends Base
{
    private function buildRememberMe($ds, array $methods = array()): RememberMe
    {
        $data = array('interval' => '+1 day');
        $rm = $this->getMockBuilder(RememberMe::class)
        ->setConstructorArgs(array($ds, $data))
        ->onlyMethods($methods)
        ->getMock();

        return $rm;
    }

    /**
    * Test the initialization of a full object with optional user
    */
    public function testInitFullObject(): void
    {
        $return = true;
        $ds = $this->buildMock($return);
        $data = array('interval' => 86400);
        $user = new UserModel($ds, array(
            'id' => 1234
        ));

        $remember = new RememberMe($ds, $data, $user);
        $this->assertEquals($remember->getData(), $data);
        $this->assertEquals($remember->getUser(), $user);
        $this->assertEquals($remember->getExpireInterval(), $data['interval']);
    }

    /**
    * Test the valid setup of the "remember me" handling for the
    *  given user (does not set cookies)
    */
    public function testSetupUserRememberValid(): void
    {
        $ds = $this->buildMock(true);
        $data = array('interval' => '+1 day');
        $user = new UserModel($ds, array('id' => 1234));
        $token = new AuthTokenModel($ds);

        $rm = $this->getMockBuilder(RememberMe::class)
        ->setConstructorArgs(array($ds, $data))
        ->onlyMethods(array('saveToken', 'getUserToken', 'setCookies'))
        ->getMock();

        $rm->method('saveToken')->willReturn($token);
        $rm->method('getUserToken')->willReturn($token);
        $rm->method('setCookies')->willReturn(true);

        $this->assertTrue($rm->setup($user));
    }

    /**
    * Test the setup of "remember me" for a user where the auth
    *  token has expired
    */
    public function testSetupUserRememberExpired(): void
    {
        $ds = $this->buildMock(true);
        $rm = $this->buildRememberMe(
            $ds, array('saveToken', 'getUserToken', 'setCookies', 'deleteToken')
        );
        $token = new AuthTokenModel($ds, array(
            'expires' => '-1 day'
        ));
        $user = new UserModel($ds, array('id' => 1234));

        $rm->method('saveToken')->willReturn($token);
        $rm->method('getUserToken')->willReturn($token);
        $rm->method('setCookies')->willReturn(true);
        $rm->method('deleteToken')->willReturn(true);

        $this->assertFalse($rm->setup($user));
    }

    /**
    * Test when there's a save error on creating the new remember me auth token
    */
    public function testSetupUserRememberNoSave(): void
    {
        $ds = $this->buildMock(true);
        $rm = $this->buildRememberMe(
            $ds, array('saveToken', 'getUserToken')
        );
        $user = new UserModel($ds, array('id' => 1234));
        $token = new AuthTokenModel($ds, array(
            'expires' => '+1 day'
        ));

        $rm->method('saveToken')->willReturn(false);
        $rm->method('getUserToken')->willReturn($token);

        $this->assertFalse($rm->setup($user));
    }

    /**
    * Test the setup of "remember me" for a user logging out
    */
    public function testSetupUserRememberLogout(): void
    {
        $ds = $this->buildMock(true);
        $rm = $this->buildRememberMe(
            $ds, array('saveToken', 'getUserToken', 'setCookies', 'destroyToken')
        );
        $token = new AuthTokenModel($ds, array(
            'expires' => '-1 day'
        ));
        $user = new UserModel($ds, array('id' => 1234));

        $rm->method('saveToken')->willReturn($token);
        $rm->method('getUserToken')->willReturn($token);
        $rm->method('setCookies')->willReturn(true);
        $rm->method('destroyToken')->willReturn(true);

        $this->assertFalse($rm->setup($user));
    }

    /**
    * Find a token instance using the token string value
    */
    public function testGetTokenByTokenValue(): void
    {
        $tokenString = md5('test1234');
        $token = new AuthTokenModel($this->buildMock(true), array(
            'expires' => '+1 day'
        ));
        $ds = $this->buildMock($token);
        $rm = $this->buildRememberMe($ds, array('saveToken'));

        $result = $rm->getByToken($tokenString);
        $this->assertEquals($result, $token);
    }

    /**
    * Find a token instance using the token string value
    */
    public function testGetTokenByTokenId(): void
    {
        $tokenId = 1234;
        $token = new AuthTokenModel($this->buildMock(true), array(
            'expires' => '+1 day'
        ));
        $ds = $this->buildMock($token);
        $rm = $this->buildRememberMe($ds, array('saveToken'));

        $result = $rm->getById($tokenId);
        $this->assertEquals($result, $token);
    }

    /**
    * Test locating a token by the given user object
    */
    public function testGetTokenByUser(): void
    {
        $user = new UserModel($this->buildMock(true), array('id' => 1234));
        $token = new AuthTokenModel($this->buildMock(true), array(
            'expires' => '+1 day',
            'userId' => $user->id
        ));

        $ds = $this->buildMock($token);
        $rm = $this->buildRememberMe($ds, array('saveToken'));

        $result = $rm->getUserToken($user);
        $this->assertEquals($result, $token);
    }

    /**
    * Test the saving of a token value, that it returns a token instance
    *  on success
    */
    public function testSaveTokenValue(): void
    {
        $tokenString = 1234;
        $user = new UserModel($this->buildMock(true), array('id' => 1234));
        $token = new AuthTokenModel($this->buildMock(true), array(
            'expires' => date('Y-m-d H:i:s', time() + 86400),
            'token' => $tokenString,
            'userId' => $user->id
        ));

        $ds = $this->buildMock(true, 'save');
        $rm = $this->buildRememberMe($ds, array('deleteToken'));

        $result = $rm->saveToken($tokenString, $user);
        $this->assertEquals($result, $token);
    }
}
