<?php

namespace Modules\Order\app\Services\Api;

use Modules\Order\app\DTOs\WishlistFilterDTO;
use Modules\Order\app\Interfaces\Api\WishlistRepositoryInterface;

class WishlistService
{
    protected $wishlistRepository;

    public function __construct(WishlistRepositoryInterface $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * Get all wishlist items for a customer with pagination
     */
    public function getCustomerWishlist(WishlistFilterDTO $dto, $customerId)
    {
        return $this->wishlistRepository->getCustomerWishlist($dto->toArray(), $customerId);
    }

    /**
     * Get a single wishlist item
     */
    public function getWishlistItemById($customerId, $id)
    {
        return $this->wishlistRepository->getWishlistItemById($customerId, $id);
    }

    /**
     * Add a product to wishlist
     */
    public function addToWishlist($customerId, $vendorProductId)
    {
        return $this->wishlistRepository->addToWishlist($customerId, $vendorProductId);
    }

    /**
     * Remove a product from wishlist
     */
    public function removeFromWishlist($customerId, $vendorProductId)
    {
        return $this->wishlistRepository->removeFromWishlist($customerId, $vendorProductId);
    }

    /**
     * Remove all items from wishlist
     */
    public function clearWishlist($customerId)
    {
        return $this->wishlistRepository->clearWishlist($customerId);
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($customerId, $vendorProductId): bool
    {
        return $this->wishlistRepository->isInWishlist($customerId, $vendorProductId);
    }

    /**
     * Get wishlist count for customer
     */
    public function getWishlistCount($customerId): int
    {
        return $this->wishlistRepository->getWishlistCount($customerId);
    }
}
