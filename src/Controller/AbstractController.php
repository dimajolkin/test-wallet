<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseAbstractController
{
    protected const GROUPS = ['rest'];

    public function notFoundResponse(): JsonResponse
    {
        return $this->json([
            'message' => 'not found',
        ], Response::HTTP_NOT_FOUND);
    }

    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return parent::json($data, $status, $headers, array_merge($context, ['groups' => static::GROUPS]));
    }
}
