<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Component\Security\Core\Authorization\Voter\AbstractCrudVoter;
use App\Dto\Assembler\AssemblerFactoryInterface;
use App\Dto\Assembler\Exception\DtoIdentifierNotFoundException;
use App\Dto\Request\DtoResourceInterface;
use App\Service\CrudServiceInterface;
use App\Service\Exception\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * Create a new resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
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

    /**
     * Update a resource by its identifier.
     *
     * This action can handle PUT and PATCH update.
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, int $id): JsonResponse
    {
        try {
            /** @var DtoResourceInterface $requestDto */
            $requestDto = $this->serializer->deserialize(
                $request->getContent(),
                $this->getDtoClassName(),
                'json',
                [
                    'dto_id' => $id,
                ]
            );
        } catch (DtoIdentifierNotFoundException $e) {
            throw new NotFoundHttpException('Resource not found.');
        }

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

    /**
     * Delete a resource by its identifier.
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, int $id): JsonResponse
    {
        $entity = $this->crudService->get($id);

        if (null === $entity) {
            throw new NotFoundHttpException('Resource not found.');
        }

        if (!$this->authorizationChecker->isGranted(AbstractCrudVoter::DELETE, $entity)) {
            throw new AccessDeniedHttpException('You have no access to delete the resource.');
        }

        try {
            $this->crudService->delete($id);
        } catch (ServiceException $e) {
            throw new HttpException(500, 'Error occurred while trying to delete the resource.');
        }

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Get a resource by its identifier.
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getAction(Request $request, int $id): JsonResponse
    {
        $entity = $this->crudService->get($id);

        if (null === $entity) {
            throw new NotFoundHttpException('Resource not found.');
        }

        if (!$this->authorizationChecker->isGranted(AbstractCrudVoter::VIEW, $entity)) {
            throw new AccessDeniedHttpException('You have no access to get the resource.');
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
