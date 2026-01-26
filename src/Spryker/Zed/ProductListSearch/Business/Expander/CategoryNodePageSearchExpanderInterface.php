<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Business\Expander;

use Generated\Shared\Transfer\NodeTransfer;

interface CategoryNodePageSearchExpanderInterface
{
    /**
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\NodeTransfer $nodeTransfer
     *
     * @return array<string, mixed>
     */
    public function expandCategoryNodePageSearchDataWithProductLists(
        array $data,
        NodeTransfer $nodeTransfer
    ): array;
}
