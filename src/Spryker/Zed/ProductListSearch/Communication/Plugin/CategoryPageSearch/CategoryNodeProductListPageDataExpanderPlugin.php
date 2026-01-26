<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Communication\Plugin\CategoryPageSearch;

use Generated\Shared\Transfer\NodeTransfer;
use Spryker\Zed\CategoryPageSearchExtension\Dependency\Plugin\CategoryNodePageSearchDataExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\ProductListSearch\Business\ProductListSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductListSearch\ProductListSearchConfig getConfig()
 * @method \Spryker\Zed\ProductListSearch\Communication\ProductListSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductListSearch\Business\ProductListSearchBusinessFactory getBusinessFactory()
 */
class CategoryNodeProductListPageDataExpanderPlugin extends AbstractPlugin implements CategoryNodePageSearchDataExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Expands provided category node page search data with product list whitelist IDs.
     * - Queries all product list whitelist IDs for products within the category.
     *
     * @api
     *
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\NodeTransfer $nodeTransfer
     * @param string $storeName
     * @param string $localeName
     *
     * @return array<string, mixed>
     */
    public function expandCategoryNodePageSearchData(
        array $data,
        NodeTransfer $nodeTransfer,
        string $storeName,
        string $localeName
    ): array {
        return $this->getBusinessFactory()
            ->createCategoryNodePageSearchExpander()
            ->expandCategoryNodePageSearchDataWithProductLists($data, $nodeTransfer);
    }
}
