<?php

declare(strict_types = 1);

use App\DataFixtures\EmailVerificationRequestFixtureLoader;

class VerificationCest
{
    /**
     * @var \App\Component\Doctrine\Common\DataFixtures\AbstractFixture
     */
    private $fixture;

    public function _before(FunctionalTester $I): void
    {
        $this->fixture = $I->loadFixture(EmailVerificationRequestFixtureLoader::class);
    }

    public function testVerification(FunctionalTester $I): void
    {
        /** @var \App\Entity\EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->fixture->getReference(
            EmailVerificationRequestFixtureLoader::REF_PENDING_REQUEST,
            4
        );

        $I->haveContentTypeJson();
        $I->sendPOST('/security/verification', [
            'id' => $verificationRequest->getId(),
            'token' => $verificationRequest->getToken(),
        ]);

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'createdAt' => 'string',
        ]);
        $I->seeResponseContainsJson([
            'id' => $verificationRequest->getUser()->getId(),
            'email' => $verificationRequest->getUser()->getEmail(),
            'role' => 'INTERVIEWER',
            'status' => 'ENABLED',
        ]);
    }

    public function testVerificationWithIncorrectId(FunctionalTester $I): void
    {
        /** @var \App\Entity\EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->fixture->getReference(
            EmailVerificationRequestFixtureLoader::REF_PENDING_REQUEST,
            4
        );

        $I->haveContentTypeJson();
        $I->sendPOST('/security/verification', [
            'id' => $verificationRequest->getId() + 100,
            'token' => $verificationRequest->getToken(),
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'validationErrors' => [
                [
                    'property' => 'token',
                    'message' => 'Request token is not valid.',
                ],
            ],
        ]);
    }

    public function testVerificationWithIncorrectToken(FunctionalTester $I): void
    {
        /** @var \App\Entity\EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->fixture->getReference(
            EmailVerificationRequestFixtureLoader::REF_PENDING_REQUEST,
            4
        );

        $I->haveContentTypeJson();
        $I->sendPOST('/security/verification', [
            'id' => $verificationRequest->getId(),
            'token' => 'incorrect-token',
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'validationErrors' => [
                [
                    'property' => 'token',
                    'message' => 'Request token is not valid.',
                ],
            ],
        ]);
    }
}
