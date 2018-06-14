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
     *     path="/interviews",
     *     methods={"POST"},
     *     name="interviews/create"
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
     *     path="/interviews/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"PUT"},
     *     name="interviews/update",
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
     *     path="/interviews/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"DELETE"},
     *     name="interviews/delete",
     * )
     */
    public function deleteAction(Request $request, int $id): JsonResponse
    {
        return parent::deleteAction($request, $id);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/interviews/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"GET"},
     *     name="interviews/get",
     * )
     */
    public function getAction(Request $request, int $id): JsonResponse
    {
        return parent::getAction($request, $id);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route(
     *     path="/interviews",
     *     requirements={"id"="\d+"},
     *     methods={"GET"},
     *     name="interviews/getList",
     * )
     */
    public function getListAction(Request $request): JsonResponse
    {
        return parent::getListAction($request);
    }
}
