<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Component\Security\Core\User\UserInterface;
use App\Dto\Assembler\Token\TokenDtoAssembler;
use App\Dto\Assembler\UserAssembler;
use App\Dto\Request\EmailVerificationToken;
use App\Dto\Request\RegistrationEmailPassword;
use App\Service\Security\SecurityServiceInterface;
use App\Service\Security\Exception\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        try {
            $user = $this->securityService->register($dto->getEmail(), $dto->getPassword());
        } catch (ServiceException $e) {
            throw new HttpException(500, 'Error occurred while trying to register.', $e);
        }

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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/security/verification",
     *     methods={"POST"},
     *     name="security/verification"
     * )
     */
    public function verificationAction(Request $request): JsonResponse
    {
        /** @var EmailVerificationToken $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            EmailVerificationToken::class,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (\count($errors) > 0) {
            throw new UnprocessableEntityHttpException($errors);
        }

        try {
            $user = $this->securityService->verify($dto->getId());
        } catch (ServiceException $e) {
            throw new HttpException(500, 'Error occurred while trying to verify an email.', $e);
        }

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

    /**
     * Currently this action not doing anything because the token issue is handling
     * by `AuthenticationSuccessHandler` from LexikJWTAuthenticationBundle.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/security/authentication",
     *     methods={"POST"},
     *     name="security/authentication"
     * )
     */
    public function authenticationAction(Request $request): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $this->getUser();
        $token = $this->securityService->issueToken($user->getIdentifier());

        return new JsonResponse(
            $this->serializer->serialize(
                (new TokenDtoAssembler($token))->writeDto('v1'),
                'json'
            ),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
