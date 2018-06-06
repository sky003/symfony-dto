<?php

declare(strict_types = 1);

use App\DataFixtures\UserFixtureLoader;

class EmailPasswordAuthenticationCest
{
    /**
     * @var \App\Component\Doctrine\Common\DataFixtures\AbstractFixture
     */
    private $fixture;

    public function _before(FunctionalTester $I): void
    {
        $this->fixture = $I->loadFixture(UserFixtureLoader::class);
    }

    public function testAuthentication(FunctionalTester $I): void
    {
        /** @var \App\Entity\User $user */
        $user = $this->fixture->getReference(UserFixtureLoader::REF_ENABLED_INTERVIEWER, 1);

        $I->haveContentTypeJson();
        $I->sendPOST('/security/authentication', [
            'email' => $user->getEmail(),
            'password' => 'password1',
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'accessToken' => 'string',
            'accessTokenExpiresIn' => 'string',
        ]);
    }

    public function testAuthenticationWithIncorrectEmail(FunctionalTester $I): void
    {
        $I->haveContentTypeJson();
        $I->sendPOST('/security/authentication', [
            'email' => 'incorrect.email#example.com',
            'password' => 'password1',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Wrong email or password.',
        ]);
    }

    public function testAuthenticationWithIncorrectPassword(FunctionalTester $I): void
    {
        /** @var \App\Entity\User $user */
        $user = $this->fixture->getReference(UserFixtureLoader::REF_ENABLED_INTERVIEWER, 1);

        $I->haveContentTypeJson();
        $I->sendPOST('/security/authentication', [
            'email' => $user->getEmail(),
            'password' => 'pass',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Wrong email or password.',
        ]);
    }

    public function testAuthenticationWithWrongEmail(FunctionalTester $I): void
    {
        $I->haveContentTypeJson();
        $I->sendPOST('/security/authentication', [
            'email' => 'incorrect.email@example.com',
            'password' => 'password1',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Wrong email or password.',
        ]);
    }

    public function testAuthenticationWithWrongPassword(FunctionalTester $I): void
    {
        /** @var \App\Entity\User $user */
        $user = $this->fixture->getReference(UserFixtureLoader::REF_ENABLED_INTERVIEWER, 1);

        $I->haveContentTypeJson();
        $I->sendPOST('/security/authentication', [
            'email' => $user->getEmail(),
            'password' => 'wrong-password',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Wrong email or password.',
        ]);
    }
}
