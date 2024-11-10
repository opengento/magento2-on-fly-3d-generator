<?php

namespace Opengento\ImageGenerator\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;

class ViewerGlb extends Template
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var string
     */
    protected $glbFilePath = 'models/';

    /**
     *
     * @var string
     */
    protected $glbFileName;

    protected $_registry;

    /**
     * @param Template\Context       $context
     * @param Filesystem             $filesystem
     * @param StoreManagerInterface  $storeManager
     * @param Registry               $registry
     * @param array                  $data
     */
    public function __construct(
        Template\Context $context,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        Registry $registry,
        array $data = []
    ) {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * check if glb exist & retrieve url or return false
     *
     * @return string|false
     */
    public function getGlbFileUrl()
    {
        $product = $this->getProduct();
        if (!$product) {
            return false;
        }

        $this->glbFileName = $product->getId() . '.glb';

        $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $filePath = $mediaDirectory->getAbsolutePath($this->glbFilePath . $this->glbFileName);

        if (file_exists($filePath)) {
            $fileUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $this->glbFilePath . $this->glbFileName;
            return $fileUrl;
        }

        return false;
    }

    /**
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }
}
