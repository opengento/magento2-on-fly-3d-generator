<?php

declare(strict_types=1);

namespace Opengento\ImageGenerator\Model\Image;

use Magento\Framework\MessageQueue\PublisherInterface;
use Opengento\ImageGenerator\Api\Data\ImageInterface;

class RetrievePublisher
{
    const TOPIC_NAME = 'opengento.image.retrieve';

    /**
     * @param PublisherInterface $publisher
     */
    public function __construct(
        private PublisherInterface $publisher
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ImageInterface $image)
    {
        $this->publisher->publish(self::TOPIC_NAME, $image);
    }
}
