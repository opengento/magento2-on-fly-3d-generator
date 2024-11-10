<?php

declare(strict_types=1);

namespace Opengento\ImageGenerator\Model\Image;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Opengento\ImageGenerator\Api\Data\ImageInterface;
use Opengento\ImageGenerator\Model\Image\RetrievePublisher;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use GuzzleHttp\Client;

class RetrieveConsumer
{
    public function __construct(
        private LoggerInterface $logger,
        private Client $client,
        private RetrievePublisher $retrievePublisher,
        private Json $serializer,
        private ProductAttributeMediaGalleryManagementInterface $productAttributeMediaGalleryManagement,
        private ProductAttributeMediaGalleryEntryInterfaceFactory $productAttributeMediaGalleryEntryFactory
    ) {
    }

    /**
     * @param ImageInterface $image
     * @return void
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function processMessage(ImageInterface $image): void
    {
        try {
            $response = $this->client->request('GET', 'http://host.docker.internal:3000/api/getImage', [
                'query' => [
                    'image_id' => $image->getImageId()
                ]
            ]);
        } catch (\Exception $e) {
            $this->retrievePublisher->execute($image);
            $this->logger->error('An error occurred while processing the message.');
            throw new LocalizedException(__('An error occurred while processing the message.'));
        }

        if ($response->getStatusCode() !== 200) {
            $this->retrievePublisher->execute($image);
            $this->logger->error('An error occurred while processing the message.');
            throw new LocalizedException(__('An error occurred while processing the message.'));
        }

        $responseData = $this->serializer->unserialize($response->getBody()->getContents());
        if ($responseData['status'] !== 'SUCCEEDED') {
            $this->retrievePublisher->execute($image);
            $this->logger->error('The image has not already been generated.');
            throw new LocalizedException(__('The image has not already been generated.'));
        }

        try {
            $usdzUrl = $responseData['model_urls']['usdz'];
            $usdzResponse = $this->client->request('GET', $usdzUrl);
            $usdzContent = $usdzResponse->getBody()->getContents();
            $usdzBase64 = base64_encode($usdzContent);
        } catch (\Exception $e) {
            $this->retrievePublisher->execute($image);
            $this->logger->error('An error occurred while retrieving the USDZ content.');
            throw new LocalizedException(__('An error occurred while retrieving the USDZ content.'));
        }

        $entry = $this->productAttributeMediaGalleryEntryFactory->create();
        $entry->setData([
            'file' => $usdzBase64,
            'media_type' => 'application/octet-stream',
            'label' => '3D Model',
            'disabled' => 0,
            'types' => ['usdz'],
            'content' => [
                'type' => 'application/octet-stream',
                'name' => '3D Model',
                'base64_encoded_data' => $usdzBase64
            ]
        ]);

        try {
            $this->productAttributeMediaGalleryManagement->create($image->getProductSku(), $entry);
        } catch (\Exception $e) {
            $this->retrievePublisher->execute($image);
            $this->logger->error('An error occurred while creating the media gallery entry.');
            throw new LocalizedException(__('An error occurred while creating the media gallery entry.'));
        }
    }
}
