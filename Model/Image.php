<?php

declare(strict_types=1);

namespace Opengento\ImageGenerator\Model;

use Magento\Framework\DataObject;
use Opengento\ImageGenerator\Api\Data\ImageInterface;

class Image extends DataObject implements ImageInterface
{
    public function getImageId(): string
    {
        return $this->getData(self::IMAGE_ID);
    }

    public function setImageId(string $imageId): ImageInterface
    {
        $this->setData(self::IMAGE_ID, $imageId);

        return $this;
    }

    public function getProductSku(): string
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    public function setProductSku(string $productId): ImageInterface
    {
        $this->setData(self::PRODUCT_SKU, $productId);

        return $this;
    }
}
