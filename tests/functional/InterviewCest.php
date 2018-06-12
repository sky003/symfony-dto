<?php

declare(strict_types = 1);

namespace App\Tests\functional;

use App\DataFixtures\InterviewFixtureLoader;
use App\Entity\Interview;
use FunctionalTester;

class InterviewCest
{
    /**
     * @var \App\Component\Doctrine\Common\DataFixtures\AbstractFixture
     */
    private $fixture;

    public function _before(FunctionalTester $I): void
    {
        $this->fixture = $I->loadFixture(InterviewFixtureLoader::class);
    }

    public function testCreate(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 2);

        $data = [
            'name' => 'Some new interview.',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendPOST('/interview', $data);

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'createdAt' => 'string',
        ]);
        $I->seeResponseContainsJson([
            'name' => $data['name'],
        ]);
    }

    public function testCreateWithoutAuthenticationCredentials(FunctionalTester $I): void
    {
        $data = [
            'name' => 'Some new interview.',
        ];

        $I->haveContentTypeJson();
        $I->sendPOST('/interview', $data);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Authentication credentials not provided.',
        ]);
    }

    public function testCreateWithIncorrectAuthenticationCredentials(FunctionalTester $I): void
    {
        $data = [
            'name' => 'Some new interview.',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => -1,
            ])
        );
        $I->sendPOST('/interview', $data);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Token authentication failed.',
        ]);
    }

    public function testCreateWithIncorrectData(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 2);

        $data = [
            'noName' => 'Some new interview.',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendPOST('/interview', $data);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'validationErrors' => [
                [
                    'property' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testUpdate(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 1);

        $data = [
            'name' => 'Updated interview name.',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendPUT('/interview/'.$interview->getId(), $data);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'updatedAt' => 'string',
        ]);
        $I->seeResponseContainsJson([
            'id' => $interview->getId(),
            'name' => $data['name'],
            'intro' => $interview->getIntro(),
        ]);
    }

    public function testUpdateWithNullableData(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 1);

        $data = [
            'intro' => 'Some really long intro...',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendPUT('/interview/'.$interview->getId(), $data);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'updatedAt' => 'string',
        ]);
        $I->seeResponseContainsJson([
            'id' => $interview->getId(),
            'name' => $interview->getName(),
            'intro' => $data['intro'],
        ]);
    }

    public function testUpdateWithoutAccessToUpdate(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 1);

        $data = [
            'name' => 'Updated interview name.',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId() + 1, // User that definitely haven't credentials to update this entity.
            ])
        );
        $I->sendPUT('/interview/'.$interview->getId(), $data);

        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'You have no access to update the resource.',
        ]);
    }

    public function testUpdateWithIncorrectData(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 4);

        $data = [
            'name' => null,
            'intro' => 'Some long intro...',
        ];

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendPUT('/interview/'.$interview->getId(), $data);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'validationErrors' => [
                [
                    'property' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }
}
