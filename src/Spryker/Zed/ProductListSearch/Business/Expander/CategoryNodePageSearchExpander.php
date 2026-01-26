<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Business\Expander;

use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\NodeTransfer;
use Spryker\Zed\ProductListSearch\Persistence\ProductListSearchRepositoryInterface;

class CategoryNodePageSearchExpander implements CategoryNodePageSearchExpanderInterface
{
    public function __construct(protected ProductListSearchRepositoryInterface $productListSearchRepository)
    {
    }

    /**
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\NodeTransfer $nodeTransfer
     *
     * @return array<string, mixed>
     */
    public function expandCategoryNodePageSearchDataWithProductLists(
        array $data,
        NodeTransfer $nodeTransfer
    ): array {
        $nodeTransfer->requireFkCategory();

        $whitelistIds = $this->productListSearchRepository->getProductListWhitelistIdsByIdCategory(
            $nodeTransfer->getFkCategoryOrFail(),
        );

        if (count($whitelistIds) > 0) {
            $data[PageIndexMap::PRODUCT_LISTS_WHITELISTS] = $whitelistIds;
        }

        return $data;
    }
}
