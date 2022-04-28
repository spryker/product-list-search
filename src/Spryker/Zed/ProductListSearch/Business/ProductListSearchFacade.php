<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Business;

use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductListMapTransfer;
use Generated\Shared\Transfer\ProductPageLoadTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\ProductListSearch\Business\ProductListSearchBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductListSearch\Persistence\ProductListSearchRepositoryInterface getRepository()
 */
class ProductListSearchFacade extends AbstractFacade implements ProductListSearchFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $concreteIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByConcreteIds(array $concreteIds): array
    {
        return $this->getFactory()
            ->createProductAbstractReader()
            ->getProductAbstractIdsByConcreteIds($concreteIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByCategoryIds(array $categoryIds): array
    {
        return $this->getFactory()
            ->createProductAbstractReader()
            ->getProductAbstractIdsByCategoryIds($categoryIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductConcretePageSearchTransfer $productConcretePageSearchTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcretePageSearchTransfer
     */
    public function expandProductConcretePageSearchTransferWithProductLists(
        ProductConcretePageSearchTransfer $productConcretePageSearchTransfer
    ): ProductConcretePageSearchTransfer {
        return $this->getFactory()
            ->createProductConcretePageSearchExpander()
            ->expandProductConcretePageSearchTransferWithProductLists($productConcretePageSearchTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<string, mixed> $productData
     * @param \Generated\Shared\Transfer\ProductListMapTransfer $productListMapTransfer
     *
     * @return \Generated\Shared\Transfer\ProductListMapTransfer
     */
    public function mapProductDataToProductListMapTransfer(array $productData, ProductListMapTransfer $productListMapTransfer): ProductListMapTransfer
    {
        return $this->getFactory()
            ->createProductDataToProductListMapTransferMapper()
            ->mapProductDataToProductListMapTransfer($productData, $productListMapTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductPageLoadTransfer $loadTransfer
     *
     * @return \Generated\Shared\Transfer\ProductPageLoadTransfer
     */
    public function expandProductPageData(ProductPageLoadTransfer $loadTransfer): ProductPageLoadTransfer
    {
        return $this->getFactory()->createProductPageDataExpander()->expandProductPageData($loadTransfer);
    }
}
