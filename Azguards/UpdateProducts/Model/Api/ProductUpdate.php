<?php

namespace Azguards\UpdateProducts\Model\Api;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;

class ProductUpdate
{

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * UpdateProduct constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository, RequestInterface $request)
    {
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    public function setProductData(): void
    {
        $sku = $this->request->getParam('sku');
        $product = $this->productRepository->get($sku);
        $data = $product->getCustomProductAttribute();
        try {
            $thumbnailUrl = 'pub/media/catalog/product/s/c/test.jpg';
            $product = $this->productRepository->get($sku);
            // Update Thumbnail Image
            if (!empty($thumbnailUrl)) {
                try {
                    if ($data == 4) {
                        $product->addImageToMediaGallery('catalog/product/s/c/test.jpeg', array('image', 'small_image', 'thumbnail'), false, false);
                        $product->setStoreId(0)->save();
                        echo "Thumbnail image for product with SKU $sku updated successfully.\n";
                    } else {
                        echo "Thumbnail image for custom products only\n";
                    }
                } catch (LocalizedException $e) {
                    echo "Error updating thumbnail image: " . $e->getMessage() . "\n";
                }
            } else {
                echo "Thumbnail image URL is empty.\n";
            }
        } catch (NoSuchEntityException $e) {
            echo "Product with SKU $sku not found.\n";
        } catch (\Exception $e) {
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
    }
}
