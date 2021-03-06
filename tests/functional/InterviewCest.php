<?php

declare(strict_types = 1);

namespace App\Tests\functional;

use App\DataFixtures\InterviewFixtureLoader;
use App\Entity\Interview;
use FunctionalTester;

/**
 * This is a really detailed test, because it's tests a JWT authentication and
 * an abstract CRUD controller functionality. The next tests will be not so detailed.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
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
        $I->sendPOST('/interviews', $data);

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
        $I->sendPOST('/interviews', $data);

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
        $I->sendPOST('/interviews', $data);

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
        $I->sendPOST('/interviews', $data);

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
        $I->sendPUT('/interviews/'.$interview->getId(), $data);

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
        $I->sendPUT('/interviews/'.$interview->getId(), $data);

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
        $I->sendPUT('/interviews/'.$interview->getId(), $data);

        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'You have no access to update the resource.',
        ]);
    }

    public function testUpdateWithNonExistingIdentifier(FunctionalTester $I): void
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
        $I->sendPUT('/interviews/'.($interview->getId() + 1000), $data); // Currently we definitely haven't more then 1000 loaded interview fixtures.

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Resource not found.',
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
        $I->sendPUT('/interviews/'.$interview->getId(), $data);

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

    public function testDelete(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 3);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendDELETE('/interviews/'.$interview->getId());

        $I->seeResponseCodeIs(204);
    }

    public function testDeleteWithNonExistingIdentifier(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 4);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendDELETE('/interviews/'.($interview->getId() + 1000));

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Resource not found.',
        ]);
    }

    public function testDeleteWithoutAccessToDelete(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 1);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId() + 1, // User that definitely haven't credentials to delete this entity.
            ])
        );
        $I->sendDELETE('/interviews/'.$interview->getId());

        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'You have no access to delete the resource.',
        ]);
    }

    public function testGet(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 4);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendGET('/interviews/'.$interview->getId());

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'id' => $interview->getId(),
            'userId' => $interview->getUser()->getId(),
            'name' => $interview->getName(),
            'intro' => $interview->getIntro(),
            'createdAt' => $interview->getCreatedAt()->format(DATE_ATOM),
        ]);
    }

    public function testGetWithNonExistingIdentifier(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 4);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId(),
            ])
        );
        $I->sendGET('/interviews/'.($interview->getId() + 1000));

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Resource not found.',
        ]);
    }

    public function testGetWithoutAccessToGet(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 1);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview->getUser()->getId() + 1, // User that definitely haven't credentials to delete this entity.
            ])
        );
        $I->sendGET('/interviews/'.$interview->getId());

        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'You have no access to get the resource.',
        ]);
    }

    public function testGetList(FunctionalTester $I): void
    {
        /** @var Interview $interview */
        $interview = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW, 1);
        /** @var Interview $interview1 */
        $interview1 = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW1, 1);
        /** @var Interview $interview2 */
        $interview2 = $this->fixture->getReference(InterviewFixtureLoader::REF_ENABLED_INTERVIEW2, 1);

        $I->haveContentTypeJson();
        $I->amBearerAuthenticated(
            $I->createJwtToken([
                'sub' => $interview1->getUser()->getId(),
            ])
        );
        $I->sendGET('/interviews?limit=2');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'offset' => 0,
            'limit' => 2,
            'total' => 3,
            'embedded' => [
                [
                    'id' => $interview->getId(),
                    'userId' => $interview->getUser()->getId(),
                    'name' => $interview->getName(),
                    'intro' => $interview->getIntro(),
                    'createdAt' => $interview->getCreatedAt()->format(DATE_ATOM),
                ],
                [
                    'id' => $interview1->getId(),
                    'userId' => $interview1->getUser()->getId(),
                    'name' => $interview1->getName(),
                    'intro' => $interview1->getIntro(),
                    'createdAt' => $interview1->getCreatedAt()->format(DATE_ATOM),
                ],
            ],
        ]);
        $I->dontSeeResponseContainsJson([
            'embedded' => [
                [
                    [
                        'id' => $interview2->getId(),
                        'userId' => $interview2->getUser()->getId(),
                        'name' => $interview2->getName(),
                        'intro' => $interview2->getIntro(),
                        'createdAt' => $interview2->getCreatedAt()->format(DATE_ATOM),
                    ],
                ],
            ],
        ]);
    }
}
