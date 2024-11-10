<?php

declare(strict_types=1);

namespace Opengento\ImageGenerator\Api\Data;

interface ImageInterface
{
    public const IMAGE_ID = 'image_id';
    public const PRODUCT_SKU = 'product_sku';

    /**
     * @return string
     */
    public function getImageId(): string;

    /**
     * @param string $imageId
     * @return ImageInterface
     */
    public function setImageId(string $imageId): ImageInterface;

    /**
     * @return string
     */
    public function getProductSku(): string;

    /**
     * @param string $productId
     * @return ImageInterface
     */
    public function setProductSku(string $productId): ImageInterface;
}
