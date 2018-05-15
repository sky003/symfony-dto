<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Dto\Assembler\UserAssembler;
use App\Dto\Request\RegistrationEmailPassword;
use App\Service\Security\SecurityServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Security controller.
 *
 * Responsible for the user registration, verification and authentication.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class SecurityController extends Controller
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, SecurityServiceInterface $securityService)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->securityService = $securityService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/security/registration",
     *     methods={"POST"},
     *     name="security/registration"
     * )
     */
    public function registrationAction(Request $request): JsonResponse
    {
        /** @var RegistrationEmailPassword $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            RegistrationEmailPassword::class,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (\count($errors) > 0) {
            throw new UnprocessableEntityHttpException($errors);
        }

        $user = $this->securityService->registration($dto->getEmail(), $dto->getPassword());

        return new JsonResponse(
            $this->serializer->serialize(
                (new UserAssembler())->writeDto($user),
                'json'
            ),
            Response::HTTP_CREATED,
            [],
            true
        );
    }
}
