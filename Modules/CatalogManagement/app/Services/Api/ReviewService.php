<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\DTOs\ReviewFilterDTO;
use Modules\CatalogManagement\app\Interfaces\Api\ReviewRepositoryInterface;

class ReviewService
{
    public function __construct(
        private ReviewRepositoryInterface $reviewRepository,
        private ProductApiService $productService,
    ) {}

    /**
     * Create a new review
     */
    public function createReview(array $data, int $vendorProductId)
    {
        $data['customer_id'] = auth('sanctum')->id();
        $data['status'] = 'pending';

        $product = $this->productService->findProduct($vendorProductId);

        if(!$product)
        {
            return false;
        }

        $data["vendor_product_id"] = $product->id;
        return $this->reviewRepository->createReview($data);
    }

    /**
     * Get reviews for a specific vendor product
     */
    public function getReviewsByVendorProduct(ReviewFilterDTO $dto)
    {
        return $this->reviewRepository->getAllReviews($dto);
    }

    /**
     * Get reviews by a specific customer
     */
    public function getReviewsByCustomer(ReviewFilterDTO $dto)
    {
        return $this->reviewRepository->getAllReviews($dto);
    }

}
