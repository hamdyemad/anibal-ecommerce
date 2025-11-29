<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

use Modules\CatalogManagement\app\DTOs\ReviewFilterDTO;

interface ReviewRepositoryInterface
{
    /**
     * Create a new review
     */
    public function createReview(array $data);

    /**
     * Get all reviews with filters and pagination
     */
    public function getAllReviews(ReviewFilterDTO $dto);

}
