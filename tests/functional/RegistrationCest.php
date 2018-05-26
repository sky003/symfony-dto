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
        $this->fixture = $I->loadFixture(UserFixtureLoader::class);
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
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'createdAt' => 'string',
        ]);
        $I->seeResponseContainsJson([
            'email' => 'john.doe@example.com',
            'role' => 'INTERVIEWER',
            'status' => 'UNVERIFIED',
        ]);
    }

    public function testRegistrationWithNotUniqueEmail(FunctionalTester $I): void
    {
        /** @var \App\Entity\User $user */
        $user = $this->fixture->getReference(UserFixtureLoader::REF_ENABLED_INTERVIEWER, 2);

        $I->haveContentTypeJson();
        $I->sendPOST('/security/registration', [
            'email' => $user->getEmail(),
            'password' => 'some-strong-password',
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'validationErrors' => [
                [
                    'property' => 'email',
                    'message' => 'Email is not unique.',
                ],
            ],
        ]);
    }
}
