<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductListSearch\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductListProductConcreteRelationTransfer;
use Generated\Shared\Transfer\ProductListTransfer;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListTableMap;
use ReflectionClass;
use Spryker\Zed\ProductList\Business\ProductList\ProductListReader;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductListSearch
 * @group Business
 * @group PreloadProductListCacheByProductIdsTest
 * Add your own group annotations below this line
 */
class PreloadProductListCacheByProductIdsTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ProductListSearch\ProductListSearchBusinessTester
     */
    protected $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->clearReaderCaches();
    }

    /**
     * @dataProvider preloadProductListCacheByProductIdsDataProvider
     */
    public function testPreloadProductListCacheByProductIdsPopulatesBothCaches(
        int $productsOnWhitelistOnly,
        int $productsOnBlacklistOnly,
        int $productsOnBoth,
        int $productsOnNeither,
        int $expectedCacheSize,
    ): void {
        // Arrange
        $whitelistProductList = null;
        $blacklistProductList = null;

        if ($productsOnWhitelistOnly > 0 || $productsOnBoth > 0) {
            $whitelistProductList = $this->tester->haveProductList([
                ProductListTransfer::TYPE => SpyProductListTableMap::COL_TYPE_WHITELIST,
            ]);
        }

        if ($productsOnBlacklistOnly > 0 || $productsOnBoth > 0) {
            $blacklistProductList = $this->tester->haveProductList([
                ProductListTransfer::TYPE => SpyProductListTableMap::COL_TYPE_BLACKLIST,
            ]);
        }

        $productsWithWhitelistOnly = [];

        for ($i = 0; $i < $productsOnWhitelistOnly; $i++) {
            $productsWithWhitelistOnly[] = $this->tester->haveProduct();
        }

        $productsWithBlacklistOnly = [];

        for ($i = 0; $i < $productsOnBlacklistOnly; $i++) {
            $productsWithBlacklistOnly[] = $this->tester->haveProduct();
        }

        $productsWithBoth = [];

        for ($i = 0; $i < $productsOnBoth; $i++) {
            $productsWithBoth[] = $this->tester->haveProduct();
        }

        $productsWithNeither = [];

        for ($i = 0; $i < $productsOnNeither; $i++) {
            $productsWithNeither[] = $this->tester->haveProduct();
        }

        if ($whitelistProductList !== null) {
            $this->linkProductsToList($whitelistProductList, array_merge(
                $productsWithWhitelistOnly,
                $productsWithBoth,
            ));
        }

        if ($blacklistProductList !== null) {
            $this->linkProductsToList($blacklistProductList, array_merge(
                $productsWithBlacklistOnly,
                $productsWithBoth,
            ));
        }

        $allProductIds = array_map(
            static fn ($product) => $product->getIdProductConcrete(),
            array_merge($productsWithWhitelistOnly, $productsWithBlacklistOnly, $productsWithBoth, $productsWithNeither),
        );

        // Act
        $this->getFacade()->preloadProductListCacheByProductIds($allProductIds);

        // Assert — both caches have exactly one entry per requested product
        [$whitelistCache, $blacklistCache] = $this->getReaderCaches();

        $this->assertCount($expectedCacheSize, $whitelistCache);
        $this->assertCount($expectedCacheSize, $blacklistCache);

        foreach ($productsWithWhitelistOnly as $product) {
            $idProduct = $product->getIdProductConcrete();

            $this->assertArrayHasKey($idProduct, $whitelistCache);
            $this->assertContains($whitelistProductList->getIdProductList(), $whitelistCache[$idProduct]);
            $this->assertEmpty($blacklistCache[$idProduct]);
        }

        foreach ($productsWithBlacklistOnly as $product) {
            $idProduct = $product->getIdProductConcrete();

            $this->assertEmpty($whitelistCache[$idProduct]);
            $this->assertArrayHasKey($idProduct, $blacklistCache);
            $this->assertContains($blacklistProductList->getIdProductList(), $blacklistCache[$idProduct]);
        }

        foreach ($productsWithBoth as $product) {
            $idProduct = $product->getIdProductConcrete();

            $this->assertContains($whitelistProductList->getIdProductList(), $whitelistCache[$idProduct]);
            $this->assertContains($blacklistProductList->getIdProductList(), $blacklistCache[$idProduct]);
        }

        foreach ($productsWithNeither as $product) {
            $idProduct = $product->getIdProductConcrete();

            $this->assertEmpty($whitelistCache[$idProduct]);
            $this->assertEmpty($blacklistCache[$idProduct]);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function preloadProductListCacheByProductIdsDataProvider(): array
    {
        return [
            'empty input leaves both caches empty' => [
                'productsOnWhitelistOnly' => 0,
                'productsOnBlacklistOnly' => 0,
                'productsOnBoth' => 0,
                'productsOnNeither' => 0,
                'expectedCacheSize' => 0,
            ],
            'product on whitelist only — whitelist entry populated, blacklist entry empty' => [
                'productsOnWhitelistOnly' => 1,
                'productsOnBlacklistOnly' => 0,
                'productsOnBoth' => 0,
                'productsOnNeither' => 0,
                'expectedCacheSize' => 1,
            ],
            'product on blacklist only — blacklist entry populated, whitelist entry empty' => [
                'productsOnWhitelistOnly' => 0,
                'productsOnBlacklistOnly' => 1,
                'productsOnBoth' => 0,
                'productsOnNeither' => 0,
                'expectedCacheSize' => 1,
            ],
            'product on both lists — both cache entries populated' => [
                'productsOnWhitelistOnly' => 0,
                'productsOnBlacklistOnly' => 0,
                'productsOnBoth' => 1,
                'productsOnNeither' => 0,
                'expectedCacheSize' => 1,
            ],
            'product on no list — both cache entries are empty arrays' => [
                'productsOnWhitelistOnly' => 0,
                'productsOnBlacklistOnly' => 0,
                'productsOnBoth' => 0,
                'productsOnNeither' => 1,
                'expectedCacheSize' => 1,
            ],
            'mixed products — each cached correctly by list membership' => [
                'productsOnWhitelistOnly' => 1,
                'productsOnBlacklistOnly' => 1,
                'productsOnBoth' => 1,
                'productsOnNeither' => 1,
                'expectedCacheSize' => 4,
            ],
        ];
    }

    /**
     * Verifies that a second preload call with already-cached product IDs
     * leaves both cache entries unchanged (no re-query to the database).
     */
    public function testPreloadProductListCacheByProductIdsSkipsAlreadyCachedProducts(): void
    {
        // Arrange
        $whitelistProductList = $this->tester->haveProductList([
            ProductListTransfer::TYPE => SpyProductListTableMap::COL_TYPE_WHITELIST,
        ]);

        $firstProduct = $this->tester->haveProduct();
        $secondProduct = $this->tester->haveProduct();

        $this->linkProductsToList($whitelistProductList, [$firstProduct, $secondProduct]);

        $this->getFacade()->preloadProductListCacheByProductIds([
            $firstProduct->getIdProductConcrete(),
            $secondProduct->getIdProductConcrete(),
        ]);

        [$whitelistCacheAfterFirstPreload, $blacklistCacheAfterFirstPreload] = $this->getReaderCaches();

        // Act: preload again with only the first product (already in cache)
        $this->getFacade()->preloadProductListCacheByProductIds([$firstProduct->getIdProductConcrete()]);

        // Assert: caches are identical — the already-cached entry was not re-fetched or overwritten
        [$whitelistCacheAfterSecondPreload, $blacklistCacheAfterSecondPreload] = $this->getReaderCaches();

        $this->assertEquals($whitelistCacheAfterFirstPreload, $whitelistCacheAfterSecondPreload);
        $this->assertEquals($blacklistCacheAfterFirstPreload, $blacklistCacheAfterSecondPreload);
        $this->assertCount(2, $whitelistCacheAfterSecondPreload);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $products
     */
    protected function linkProductsToList(ProductListTransfer $productListTransfer, array $products): void
    {
        $relation = (new ProductListProductConcreteRelationTransfer())
            ->setIdProductList($productListTransfer->getIdProductList());

        foreach ($products as $product) {
            $relation->addProductIds($product->getIdProductConcrete());
        }

        $productListTransfer->setProductListProductConcreteRelation($relation);

        $this->tester->getLocator()->productList()->facade()->saveProductList($productListTransfer);
    }

    /**
     * @return array{array<int, array<int>>, array<int, array<int>>}
     */
    protected function getReaderCaches(): array
    {
        $reflection = new ReflectionClass(ProductListReader::class);

        return [
            $reflection->getProperty('productWhitelistIdsCache')->getValue(),
            $reflection->getProperty('productBlackListIdsCache')->getValue(),
        ];
    }

    protected function clearReaderCaches(): void
    {
        $reflection = new ReflectionClass(ProductListReader::class);
        $reflection->getProperty('productWhitelistIdsCache')->setValue(null, []);
        $reflection->getProperty('productBlackListIdsCache')->setValue(null, []);
    }

    /**
     * @return \Spryker\Zed\ProductListSearch\Business\ProductListSearchFacadeInterface
     */
    protected function getFacade()
    {
        return $this->tester->getFacade();
    }
}
