<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Dto\Request\Interview;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class InterviewController extends AbstractCrudController
{
    /**
     * {@inheritdoc}
     */
    public function getDtoClassName(): string
    {
        return Interview::class;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/interview",
     *     methods={"POST"},
     *     name="interview/create"
     * )
     */
    public function createAction(Request $request): JsonResponse
    {
        return parent::createAction($request);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/interview/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"PUT"},
     *     name="interview/update",
     * )
     */
    public function updateAction(Request $request, int $id): JsonResponse
    {
        return parent::updateAction($request, $id);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/interview/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"DELETE"},
     *     name="interview/delete",
     * )
     */
    public function deleteAction(Request $request, int $id): JsonResponse
    {
        return parent::deleteAction($request, $id);
    }
}
