<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Order\app\DTOs\WishlistFilterDTO;
use Modules\Order\app\Http\Requests\Api\AddToWishlistRequest;
use Modules\Order\app\Http\Requests\Api\RemoveFromWishlistRequest;
use Modules\Order\app\Services\Api\WishlistService;
use Modules\Order\app\Http\Resources\Api\WishlistResource;

class WishlistApiController extends Controller
{
    use Res;

    public function __construct(protected WishlistService $wishlistService)
    {}

    /**
     * Get customer's wishlist with pagination
     */
    public function list(Request $request)
    {
        $dto = WishlistFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $customer = $request->user();
        $wishlist = $this->wishlistService->getCustomerWishlist($dto, $customer->id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            WishlistResource::collection($wishlist)->additional($request->all())
        );
    }

    /**
     * Add product to wishlist
     */
    public function add(AddToWishlistRequest $request)
    {
        $customer = $request->user();
        $validated = $request->validated();

        $wishlistItem = $this->wishlistService->addToWishlist(
            $customer->id,
            $validated['vendor_product_id']
        );

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [],
            [],
            201
        );
    }

    /**
     * Remove product from wishlist
     */
    public function remove(RemoveFromWishlistRequest $request)
    {
        $customer = $request->user();
        $validated = $request->validated();

        $this->wishlistService->removeFromWishlist(
            $customer->id,
            $validated['vendor_product_id']
        );

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true
        );
    }

    /**
     * Clear all items from wishlist
     */
    public function clear(Request $request)
    {
        $customer = $request->user();

        $this->wishlistService->clearWishlist($customer->id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true
        );
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request)
    {
        $customer = $request->user();
        $vendorProductId = $request->input('vendor_product_id') ?? $request->input('product_id');

        if (!$vendorProductId) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                ['vendor_product_id' => [__('validation.vendor_product_id_required')]],
                422
            );
        }

        $isInWishlist = $this->wishlistService->isInWishlist(
            $customer->id,
            $vendorProductId
        );

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'is_in_wishlist' => $isInWishlist,
                'vendor_product_id' => $vendorProductId,
            ]
        );
    }

    /**
     * Get wishlist count for customer
     */
    public function count(Request $request)
    {
        $customer = $request->user();

        $count = $this->wishlistService->getWishlistCount($customer->id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'count' => $count,
            ]
        );
    }
}
