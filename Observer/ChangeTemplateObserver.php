<?php

namespace Opengento\ImageGenerator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ChangeTemplateObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $observer->getBlock()->setTemplate('Opengento_ImageGenerator::helper/gallery.phtml');
    }
}
