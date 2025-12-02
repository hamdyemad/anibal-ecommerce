<?php

namespace Modules\Order\app\Interfaces\Api;

interface WishlistRepositoryInterface
{
    /**
     * Get all wishlist items for a customer with pagination
     */
    public function getCustomerWishlist(array $data, $customerId);

    /**
     * Get a single wishlist item by ID
     */
    public function getWishlistItemById($customerId, $id);

    /**
     * Add a product to wishlist
     */
    public function addToWishlist($customerId, $vendorProductId);

    /**
     * Remove a product from wishlist
     */
    public function removeFromWishlist($customerId, $vendorProductId);

    /**
     * Remove all items from wishlist
     */
    public function clearWishlist($customerId);

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($customerId, $vendorProductId);

    /**
     * Get wishlist count for customer
     */
    public function getWishlistCount($customerId);
}
