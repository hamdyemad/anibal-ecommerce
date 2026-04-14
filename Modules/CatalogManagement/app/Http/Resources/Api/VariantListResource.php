<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PointsHelper;

class VariantListResource extends JsonResource
{
    /**
     * Transform variant into array for listing
     */
    public function toArray(Request $request): array
    {
        $vendorProduct = $this->vendorProduct;
        $product = $vendorProduct?->product;
        
        // Calculate price with taxes
        $priceBeforeTaxes = (float) $this->price;
        $taxes = $vendorProduct?->taxes ?? collect();
        $taxRate = $taxes->sum('percentage') ?? 0;
        $realPrice = $priceBeforeTaxes * (1 + ($taxRate / 100));
        
        // Calculate points
        $points = PointsHelper::calculatePoints($priceBeforeTaxes);
        
        // Get fake price if discount exists
        $fakePrice = null;
        $fakePriceWithTax = null;
        if ($this->has_discount && $this->price_before_discount) {
            $fakePrice = (float) $this->price_before_discount;
            $fakePriceWithTax = $fakePrice * (1 + ($taxRate / 100));
        }

        // Calculate discount percentage
        $discount = null;
        if ($this->has_discount && $this->price_before_discount && $priceBeforeTaxes > 0) {
            $priceBeforeDiscount = (float) $this->price_before_discount;
            if ($priceBeforeDiscount != 0) {
                $discount = round((($priceBeforeDiscount - $priceBeforeTaxes) / $priceBeforeDiscount) * 100, 2);
            }
        }

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'variant_name' => $this->variant_name,
            'vendor_product' => [
                'id' => $vendorProduct?->id,
                'slug' => $product?->slug,
                'name' => $product?->title,
                'image' => formatImage($product?->mainImage),
                'sku' => $vendorProduct?->sku,
            ],
            'points' => $points,
            'price_before_taxes' => number_format($priceBeforeTaxes, 2, '.', ''),
            'real_price' => number_format($realPrice, 2, '.', ''),
            'fake_price' => $fakePriceWithTax ? number_format($fakePriceWithTax, 2, '.', '') : null,
            'discount' => $discount,
            'total_stock' => $this->total_stock ?? 0,
            'remaining_stock' => $this->remaining_stock ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'vendor' => [
                'id' => $vendorProduct?->vendor_id,
                'name' => $vendorProduct?->vendor?->name,
                'slug' => $vendorProduct?->vendor?->slug,
            ],
            'brand' => $product?->brand ? [
                'id' => $product->brand->id,
                'title' => $product->brand->title,
                'slug' => $product->brand->slug,
            ] : null,
            'department' => $product?->department ? [
                'id' => $product->department->id,
                'name' => $product->department->name,
                'slug' => $product->department->slug,
            ] : null,
            'category' => $product?->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'sub_category' => $product?->subCategory ? [
                'id' => $product->subCategory->id,
                'name' => $product->subCategory->name,
                'slug' => $product->subCategory->slug,
            ] : null,
            'variant_tree' => $this->buildVariantTree(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Build variant tree showing configuration hierarchy using variant links
     * Returns string path like: "Model → 2027-Model → Color → Black → Size → Medium"
     */
    protected function buildVariantTree(): string
    {
        if (!$this->variantLink) {
            // Fallback to simple path if no link
            if ($this->variantConfiguration) {
                $parts = [];
                if ($this->variantConfiguration->key) {
                    $parts[] = $this->variantConfiguration->key->name;
                }
                $parts[] = $this->variantConfiguration->name;
                return implode(' → ', $parts);
            }
            return '';
        }

        $path = [];
        
        // Start from the parent config in the link
        $parentConfigId = $this->variantLink->parent_config_id;
        $path = $this->buildConfigPath($parentConfigId);
        
        // Add the child config (current variant)
        if ($this->variantConfiguration) {
            if ($this->variantConfiguration->key) {
                $path[] = $this->variantConfiguration->key->name;
            }
            $path[] = $this->variantConfiguration->name;
        }
        
        return implode(' → ', $path);
    }

    /**
     * Recursively build path for a configuration by following links
     */
    protected function buildConfigPath($configId, $depth = 0): array
    {
        if ($depth > 10 || !$configId) {
            return [];
        }

        $config = \Modules\CatalogManagement\app\Models\VariantsConfiguration::with('key')->find($configId);
        if (!$config) {
            return [];
        }

        $path = [];
        
        // Check if this config has a parent link
        $parentLink = \Illuminate\Support\Facades\DB::table('variants_configurations_links')
            ->where('child_config_id', $configId)
            ->first();
        
        if ($parentLink) {
            // Recursively get parent path
            $path = $this->buildConfigPath($parentLink->parent_config_id, $depth + 1);
        } elseif ($config->parent_id) {
            // Fallback to parent_id if no link
            $path = $this->buildConfigPath($config->parent_id, $depth + 1);
        }
        
        // Add current config
        if ($config->key) {
            $path[] = $config->key->name;
        }
        $path[] = $config->name;
        
        return $path;
    }
}
