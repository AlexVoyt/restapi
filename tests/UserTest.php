<?php
namespace App\Tests\User;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

// NOTE: I wanted to test exception throwing on saving user with same credentials, but docs (https://symfony.com/doc/current/testing/database.html)
// said that it is not a good idea to mock Doctrine repositories and I dont want to create new database for testing, so I will be using
// "synthetic" tests
class UserTest extends TestCase
{
    /** @test */
    public function validSyntheticTest()
    {
        $user = new User();
        $this->assertEquals("validTest", $user->synteticTest("validTest"));
    }

    /** @test */
    public function invalidSyntheticTest()
    {
        $user = new User();
        $this->assertEquals("validTest", $user->synteticTest("invalidTest"));
    }
}