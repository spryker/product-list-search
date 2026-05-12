<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Communication\Plugin\ProductPageSearch;

use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductPageSearchExtension\Dependency\Plugin\ProductConcretePageDataExpanderPluginInterface;
use Spryker\Zed\ProductPageSearchExtension\Dependency\Plugin\ProductConcretePageDataExpanderPreloaderPluginInterface;

/**
 * @method \Spryker\Zed\ProductListSearch\Business\ProductListSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductListSearch\ProductListSearchConfig getConfig()
 * @method \Spryker\Zed\ProductListSearch\Communication\ProductListSearchCommunicationFactory getFactory()
 */
class ProductConcreteProductListPageDataExpanderPlugin extends AbstractPlugin implements ProductConcretePageDataExpanderPluginInterface, ProductConcretePageDataExpanderPreloaderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return void
     */
    public function preload(array $productConcreteTransfers): void
    {
        $productIds = array_map(
            static fn (ProductConcreteTransfer $productConcreteTransfer): int => $productConcreteTransfer->getIdProductConcreteOrFail(),
            $productConcreteTransfers,
        );

        $this->getFacade()->preloadProductListCacheByProductIds($productIds);
    }

    /**
     * {@inheritDoc}
     * - Expands provided ProductConcretePageSearchTransfer with product lists ids.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     * @param \Generated\Shared\Transfer\ProductConcretePageSearchTransfer $productConcretePageSearchTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcretePageSearchTransfer
     */
    public function expand(
        ProductConcreteTransfer $productConcreteTransfer,
        ProductConcretePageSearchTransfer $productConcretePageSearchTransfer
    ): ProductConcretePageSearchTransfer {
        $productConcretePageSearchTransfer->setFkProduct(
            $productConcreteTransfer->getIdProductConcrete(),
        );

        return $this->getFacade()->expandProductConcretePageSearchTransferWithProductLists($productConcretePageSearchTransfer);
    }
}
