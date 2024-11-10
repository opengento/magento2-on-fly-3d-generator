<?php
declare(strict_types=1);

namespace Opengento\ImageGenerator\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Opengento\ImageGenerator\Api\Data\ImageInterfaceFactory;
use Opengento\ImageGenerator\Model\Image\RetrievePublisher;

class Generate extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_ImageGenerator::generate_image';

    public function __construct(
        Context $context,
        private RetrievePublisher $retrievePublisher,
        public ImageInterfaceFactory $imageFactory,
        private JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $imageId = $this->getRequest()->getParam('image_id');
        $productSku = $this->getRequest()->getParam('product_sku');
        if ($productSku === null || $imageId === null) {
            return $this->jsonResultFactory->create()->setData(['success' => false]);
        }

        $image = $this->imageFactory->create();
        $image->setImageId($imageId);
        $image->setProductSku($productSku);
        $this->retrievePublisher->execute($image);

        return $this->jsonResultFactory->create()->setData(['success' => true]);
    }
}
