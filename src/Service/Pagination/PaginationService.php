<?php

namespace App\Service\Pagination;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaginationService
{


    private RequestStack $requestStack;

    /**
     *
     *  PaginationService constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     *
     * Get offset from request
     *
     * @return int
     * @throws BadRequestHttpException
     */
    public function getOffset(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request->query->filter('offset', 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    }


    /**
     *
     * Get limit from request
     *
     * @return int
     * @throws BadRequestHttpException
     */
    public function getLimit(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request->query->filter('limit', 3, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    }
}
