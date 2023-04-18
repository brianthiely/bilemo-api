<?php

namespace App\Service\Product;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaginationService
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function getOffset(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        $offset = $request->query->filter('offset', 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($offset === false) {
            throw new BadRequestHttpException('Invalid offset parameter');
        }
        return $offset;
    }

    public function getLimit(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        $limit = $request->query->filter('limit', 3, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($limit === false) {
            throw new BadRequestHttpException('Invalid limit parameter');
        }
        return $limit;
    }

}

