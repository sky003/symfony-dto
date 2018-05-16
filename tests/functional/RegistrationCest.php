<?php

declare(strict_types = 1);

use App\DataFixtures\UserFixtureLoader;

class RegistrationCest
{
    /**
     * @var \App\Component\Doctrine\Common\DataFixtures\AbstractFixture
     */
    private $fixture;

    public function _before(FunctionalTester $I): void
    {
        $this->fixture = $I->loadFixture(new UserFixtureLoader());
    }

    public function testRegistration(FunctionalTester $I): void
    {
        $I->haveContentTypeJson();
        $I->sendPOST('/security/registration', [
            'email' => 'john.doe@example.com',
            'password' => 'some-strong-password',
        ]);

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
}
