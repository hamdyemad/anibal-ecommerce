<?php

namespace Modules\Refund\app\DataTables;

use Modules\CatalogManagement\app\Helpers\VariantConfigHelper;
use Modules\Refund\app\Models\RefundRequest;

class RefundItemsDataTable
{
    /**
     * Get formatted data for refund items
     */
    public function getData(RefundRequest $refundRequest): array
    {
        $locale = app()->getLocale();
        $data = [];
        $index = 1;
        
        foreach ($refundRequest->items as $item) {
            $data[] = [
                'index' => $index++,
                'product' => $this->buildProductHtml($item, $locale),
                'price_before_tax' => $this->formatPrice($this->calculatePriceBeforeTax($item)),
                'tax' => $this->formatPrice($this->calculateTaxAmount($item)),
                'price_with_tax' => $this->formatPrice($this->calculatePriceWithTax($item)),
                'quantity' => $this->buildQuantityBadge($item->quantity),
                'shipping' => $this->formatPrice($item->shipping_amount),
                'total' => $this->buildTotalHtml($item->total_price + $item->shipping_amount),
            ];
        }
        
        return $data;
    }

    /**
     * Build product HTML with all details
     */
    protected function buildProductHtml($item, string $locale): string
    {
        $orderProduct = $item->orderProduct;
        $vendorProduct = $orderProduct?->vendorProduct;
        $product = $vendorProduct?->product;
        $variant = $orderProduct?->vendorProductVariant;
        $vendor = $vendorProduct?->vendor;
        
        $html = '<div class="d-flex align-items-center gap-3">';
        
        // Product image
        $html .= $this->buildProductImage($product, $locale);
        
        $html .= '<div class="flex-grow-1 text-start">';
        
        // Product name
        $html .= $this->buildProductName($product, $locale);
        
        // SKU
        $html .= $this->buildSku($variant);
        
        // Vendor
        $html .= $this->buildVendorInfo($vendor, $locale);
        
        // Variant configuration tree
        $html .= $this->buildVariantConfigTree($variant, $locale);
        
        $html .= '</div></div>';
        
        return $html;
    }

    /**
     * Build product image HTML
     */
    protected function buildProductImage($product, string $locale): string
    {
        if ($product && $product->mainImage) {
            return '<img src="' . asset('storage/' . $product->mainImage->path) . '" 
                         alt="' . htmlspecialchars($product->getTranslation('title', $locale)) . '" 
                         class="rounded"
                         style="width: 60px; height: 60px; object-fit: cover;">';
        }
        
        return '<div class="bg-light rounded d-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px;">
                    <i class="uil uil-image-slash text-muted"></i>
                </div>';
    }

    /**
     * Build product name HTML
     */
    protected function buildProductName($product, string $locale): string
    {
        $title = $product 
            ? htmlspecialchars($product->getTranslation('title', $locale)) 
            : trans('common.not_available');
            
        return '<div class="fw-bold mb-1">' . $title . '</div>';
    }

    /**
     * Build SKU HTML
     */
    protected function buildSku($variant): string
    {
        if (!$variant || !$variant->sku) {
            return '';
        }
        
        return '<div class="text-muted small mb-1">
            <strong>' . trans('order::order.sku') . ':</strong> ' . htmlspecialchars($variant->sku) . '
        </div>';
    }

    /**
     * Build vendor info HTML
     */
    protected function buildVendorInfo($vendor, string $locale): string
    {
        if (!$vendor) {
            return '';
        }
        
        return '<div class="text-muted small mb-1">
            <i class="uil uil-store me-1"></i>
            <strong>' . trans('order::order.vendor') . ':</strong> ' . 
            htmlspecialchars($vendor->getTranslation('name', $locale)) . '
        </div>';
    }

    /**
     * Build variant configuration tree HTML
     */
    protected function buildVariantConfigTree($variant, string $locale): string
    {
        return VariantConfigHelper::buildConfigTreeHtml($variant, $locale);
    }

    /**
     * Calculate price before tax
     */
    protected function calculatePriceBeforeTax($item): float
    {
        // unit_price is already stored WITHOUT tax
        return $item->unit_price;
    }

    /**
     * Calculate tax amount per unit
     */
    protected function calculateTaxAmount($item): float
    {
        return $item->tax_amount / $item->quantity;
    }

    /**
     * Calculate price with tax (per unit)
     */
    protected function calculatePriceWithTax($item): float
    {
        // total_price includes tax, divide by quantity to get per-unit price with tax
        return $item->total_price / $item->quantity;
    }

    /**
     * Format price with currency
     */
    protected function formatPrice(float $price): string
    {
        $currency = trans('common.currency') ?? 'EGP';
        return number_format($price, 2) . ' ' . $currency;
    }

    /**
     * Build quantity badge HTML
     */
    protected function buildQuantityBadge(int $quantity): string
    {
        return '<span class="badge badge-primary badge-lg">' . $quantity . '</span>';
    }

    /**
     * Build total price HTML
     */
    protected function buildTotalHtml(float $total): string
    {
        $currency = trans('common.currency') ?? 'EGP';
        return '<span class="fw-bold text-success">' . number_format($total, 2) . ' ' . $currency . '</span>';
    }

    /**
     * Get table headers
     */
    public function getHeaders(): array
    {
        return [
            ['label' => '#', 'class' => 'text-center'],
            ['label' => trans('refund::refund.fields.product')],
            ['label' => trans('order::order.price_before_taxes'), 'class' => 'text-center'],
            ['label' => trans('order::order.taxes'), 'class' => 'text-center'],
            ['label' => trans('order::order.price_including_taxes'), 'class' => 'text-center'],
            ['label' => trans('refund::refund.fields.quantity'), 'class' => 'text-center'],
            ['label' => trans('order::order.shipping'), 'class' => 'text-center'],
            ['label' => trans('refund::refund.fields.total_price'), 'class' => 'text-center'],
        ];
    }

    /**
     * Get table columns configuration
     */
    public function getColumns(): array
    {
        return [
            ['data' => 'index', 'orderable' => false, 'searchable' => false, 'className' => 'text-center fw-bold', 'width' => '5%'],
            ['data' => 'product', 'orderable' => false, 'searchable' => false, 'width' => '30%'],
            ['data' => 'price_before_tax', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '10%'],
            ['data' => 'tax', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '8%'],
            ['data' => 'price_with_tax', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '12%'],
            ['data' => 'quantity', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '8%'],
            ['data' => 'shipping', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '10%'],
            ['data' => 'total', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '12%'],
        ];
    }
}
