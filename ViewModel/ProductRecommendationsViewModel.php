<?php

/**
 * AlgoliaProductRecommendations
 *
 * Algolia product recommendations UI
 *
 * @package ImaginationMedia\AlgoliaProductRecommendations
 * @author Vasilii Burlacu <vasilii@imaginationmedia.com>
 * @copyright Copyright (c) 2022 Imagination Media (https://www.imaginationmedia.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace ImaginationMedia\AlgoliaProductRecommendations\ViewModel;

use Algolia\AlgoliaSearch\DataProvider\Analytics\IndexEntityDataProvider;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductRecommendationsViewModel
 */
class ProductRecommendationsViewModel implements ArgumentInterface
{
    private Registry $registry;
    private StoreManagerInterface $storeManager;
    private ConfigHelper $configHelper;
    private IndexEntityDataProvider $entityDataProvider;

    /**
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ConfigHelper $configHelper
     * @param IndexEntityDataProvider $entityDataProvider
     */
    public function __construct(
        Registry $registry,
        StoreManagerInterface $storeManager,
        ConfigHelper $configHelper,
        IndexEntityDataProvider $entityDataProvider
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->entityDataProvider = $entityDataProvider;
    }

    public function getProductId(): ?int
    {
        /** @var Product|null $product */
        $product = $this->registry->registry('current_product') ?: null;

        if (!$product) {
            return null;
        }

        return (int) $product->getId();
    }

    public function isEnabled(): bool
    {
        $storeId = $this->getStore()->getId();

        return $this->configHelper->isEnabledFrontEnd($storeId) &&
            $this->configHelper->credentialsAreConfigured($storeId);
    }

    public function getApplicationId(): string
    {
        return $this->configHelper->getApplicationID($this->getStore()->getId());
    }

    public function getSearchOnlyAPIKey(): string
    {
        return $this->configHelper->getSearchOnlyAPIKey($this->getStore()->getId());
    }

    public function getIndexName(): string
    {
        $store = $this->getStore();

        return (string) $this->entityDataProvider->getIndexNameByEntity('products', $store->getId());
    }

    private function getStore(): StoreInterface
    {
        return $this->storeManager->getStore();
    }
}
