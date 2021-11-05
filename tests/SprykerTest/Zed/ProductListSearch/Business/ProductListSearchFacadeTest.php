<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductListSearch\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductListMapTransfer;
use Generated\Shared\Transfer\ProductPageSearchTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductListSearch
 * @group Business
 * @group Facade
 * @group ProductListSearchFacadeTest
 * Add your own group annotations below this line
 */
class ProductListSearchFacadeTest extends Unit
{
    /**
     * @var int
     */
    protected const TEST_WHITELIST_KEY = 1;

    /**
     * @var int
     */
    protected const TEST_BLACKLIST_KEY = 2;

    /**
     * @var \SprykerTest\Zed\ProductListSearch\ProductListSearchBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testExpandProductConcretePageSearchTransferWithProductLists(): void
    {
        // Arrange
        $productConcretePageSearchTransfer = new ProductConcretePageSearchTransfer();
        $productConcrete = $this->tester->haveProduct();
        $productConcretePageSearchTransfer->setFkProduct($productConcrete->getIdProductConcrete());

        // Act
        $this->getFacade()->expandProductConcretePageSearchTransferWithProductLists(
            $productConcretePageSearchTransfer,
        );

        // Assert
        $this->assertInstanceOf(ProductListMapTransfer::class, $productConcretePageSearchTransfer->getProductListMap());
    }

    /**
     * @return void
     */
    public function testMapProductDataToProductListMapTransfer(): void
    {
        // Arrange
        $productData = [
            ProductPageSearchTransfer::PRODUCT_LIST_MAP => [
                ProductListMapTransfer::WHITELISTS => [static::TEST_WHITELIST_KEY],
                ProductListMapTransfer::BLACKLISTS => [static::TEST_BLACKLIST_KEY],
            ],
        ];
        $productListMapTransfer = new ProductListMapTransfer();

        // Act
        $this->getFacade()->mapProductDataToProductListMapTransfer($productData, $productListMapTransfer);

        // Assert
        $this->assertIsArray($productListMapTransfer->getWhitelists());
        $this->assertIsArray($productListMapTransfer->getBlacklists());
        $this->assertEquals([static::TEST_WHITELIST_KEY], $productListMapTransfer->getWhitelists());
        $this->assertEquals([static::TEST_BLACKLIST_KEY], $productListMapTransfer->getBlacklists());
    }

    /**
     * @return \Spryker\Zed\ProductListSearch\Business\ProductListSearchFacadeInterface
     */
    protected function getFacade()
    {
        return $this->tester->getFacade();
    }
}
