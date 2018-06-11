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
}