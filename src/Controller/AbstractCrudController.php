<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Component\Security\Core\Authorization\Voter\AbstractCrudVoter;
use App\Dto\Assembler\AssemblerFactoryInterface;
use App\Dto\Request\DtoResourceInterface;
use App\Service\CrudServiceInterface;
use App\Service\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Abstract CRUD controller to extend from.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
abstract class AbstractCrudController extends Controller
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
     * @var CrudServiceInterface
     */
    private $crudService;
    /**
     * @var AssemblerFactoryInterface
     */
    private $assemblerFactory;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * AbstractCrudController constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param CrudServiceInterface $crudService
     * @param AssemblerFactoryInterface $assemblerFactory
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, CrudServiceInterface $crudService, AssemblerFactoryInterface $assemblerFactory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->crudService = $crudService;
        $this->assemblerFactory = $assemblerFactory;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Returns a DTO type.
     *
     * Used to create a DTO object from the request content.
     *
     * @return string
     */
    abstract public function getDtoClassName(): string;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAction(Request $request): JsonResponse
    {
        return new JsonResponse();
    }

    public function createAction(Request $request): JsonResponse
    {
        /** @var DtoResourceInterface $requestDto */
        $requestDto = $this->serializer->deserialize(
            $request->getContent(),
            $this->getDtoClassName(),
            'json'
        );

        $errors = $this->validator->validate($requestDto, null, ['OpCreate']);
        if (\count($errors) > 0) {
            throw new UnprocessableEntityHttpException($errors);
        }

        $entity = $this->assemblerFactory->loadDto($requestDto)->writeEntity();

        try {
            $this->crudService->create($entity);
        } catch (ServiceException $e) {
            throw new HttpException(500, 'Error occurred while trying to create the resource.');
        }

        $responseDto = $this->assemblerFactory->loadEntity($entity)->writeDto('v1');

        return new JsonResponse(
            $this->serializer->serialize(
                $responseDto,
                'json'
            ),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    public function updateAction(Request $request, int $id): JsonResponse
    {
        /** @var DtoResourceInterface $requestDto */
        $requestDto = $this->serializer->deserialize(
            $request->getContent(),
            $this->getDtoClassName(),
            'json',
            [
                'dto_id' => $id,
            ]
        );

        $errors = $this->validator->validate($requestDto, null, ['OpUpdate']);
        if (\count($errors) > 0) {
            throw new UnprocessableEntityHttpException($errors);
        }

        $entity = $this->assemblerFactory->loadDto($requestDto)->writeEntity();

        if (!$this->authorizationChecker->isGranted(AbstractCrudVoter::EDIT, $entity)) {
            throw new AccessDeniedHttpException('You have no access to update the resource.');
        }

        try {
            $this->crudService->update($entity);
        } catch (ServiceException $e) {
            throw new HttpException(500, 'Error occurred while trying to create the resource.');
        }

        $responseDto = $this->assemblerFactory->loadEntity($entity)->writeDto('v1');

        return new JsonResponse(
            $this->serializer->serialize(
                $responseDto,
                'json'
            ),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
