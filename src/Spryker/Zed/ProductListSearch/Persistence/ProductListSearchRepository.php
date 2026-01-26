<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Persistence;

use Orm\Zed\Category\Persistence\Map\SpyCategoryNodeTableMap;
use Orm\Zed\Category\Persistence\SpyCategoryNodeQuery;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\ProductCategory\Persistence\Map\SpyProductCategoryTableMap;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListCategoryTableMap;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListProductConcreteTableMap;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListTableMap;
use Orm\Zed\ProductList\Persistence\SpyProductListCategoryQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ProductListSearch\Persistence\ProductListSearchPersistenceFactory getFactory()
 */
class ProductListSearchRepository extends AbstractRepository implements ProductListSearchRepositoryInterface
{
    /**
     * @var string
     */
    public const COL_CONCRETE_PRODUCT_COUNT = 'concrete_product_count';

    /**
     * @var string
     */
    public const COL_ID_PRODUCT_ABSTRACT = 'col_id_product_abstract';

    /**
     * @var string
     */
    public const COL_TYPE = 'col_type';

    /**
     * @var string
     */
    public const COL_ID_PRODUCT_LIST = 'col_id_product_list';

    /**
     * @return int
     */
    public function getValueForWhitelistType(): int
    {
        return $this->getEnumValueForListType(SpyProductListTableMap::COL_TYPE_WHITELIST);
    }

    /**
     * @return int
     */
    public function getValueForBlacklistType(): int
    {
        return $this->getEnumValueForListType(SpyProductListTableMap::COL_TYPE_BLACKLIST);
    }

    /**
     * @param string $listType
     *
     * @return int
     */
    protected function getEnumValueForListType(string $listType): int
    {
        /** @phpstan-var int */
        return array_search(
            $listType,
            SpyProductListTableMap::getValueSet(SpyProductListTableMap::COL_TYPE),
        );
    }

    /**
     * @uses SpyProductQuery
     *
     * @param array<int> $concreteIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByConcreteIds(array $concreteIds): array
    {
        /** @var \Orm\Zed\Product\Persistence\SpyProductQuery $productQuery */
        $productQuery = $this->getFactory()
            ->getProductQuery()
            ->select(SpyProductTableMap::COL_FK_PRODUCT_ABSTRACT);

        /** @var \Propel\Runtime\Collection\ArrayCollection $productAbstractIds */
        $productAbstractIds = $productQuery
            ->filterByIdProduct_In($concreteIds)
            ->find();

        return $productAbstractIds->toArray();
    }

    /**
     * @module ProductCategory
     *
     * @param array $categoryIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByCategoryIds(array $categoryIds): array
    {
        /** @var \Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery $productCategoryQuery */
        $productCategoryQuery = $this->getFactory()
            ->getProductCategoryPropelQuery()
            ->select(SpyProductCategoryTableMap::COL_FK_PRODUCT_ABSTRACT);

        /** @var \Propel\Runtime\Collection\ArrayCollection $productAbstractIds */
        $productAbstractIds = $productCategoryQuery
            ->filterByFkCategory_In($categoryIds)
            ->distinct()
            ->find();

        return $productAbstractIds->toArray();
    }

    /**
     * @module ProductCategory
     *
     * @param array<int, int> $categoryIdsTimestampMap
     *
     * @return array<int, int>
     */
    public function getProductAbstractIdsTimestampMap(array $categoryIdsTimestampMap): array
    {
        $productAbstractIdTimestampMap = [];

        /** @var \Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery $productCategoryQuery */
        $productCategoryQuery = $this->getFactory()
            ->getProductCategoryPropelQuery()
            ->select([SpyProductCategoryTableMap::COL_FK_PRODUCT_ABSTRACT, SpyProductCategoryTableMap::COL_FK_CATEGORY]);

        /** @var \Propel\Runtime\Collection\ArrayCollection $productAbstractData */
        $productAbstractData = $productCategoryQuery
            ->filterByFkCategory_In(array_keys($categoryIdsTimestampMap))
            ->distinct()
            ->find()
            ->getData();

        foreach ($productAbstractData as $productAbstract) {
            $productAbstractIdTimestampMap[(int)$productAbstract[SpyProductCategoryTableMap::COL_FK_PRODUCT_ABSTRACT]] =
                $categoryIdsTimestampMap[$productAbstract[SpyProductCategoryTableMap::COL_FK_CATEGORY]];
        }

        return $productAbstractIdTimestampMap;
    }

    /**
     * @uses SpyProductQuery
     *
     * @param array<int, int> $concreteIdsTimestampMap
     *
     * @return array<int, int>
     */
    public function getProductAbstractIdsTimestampMapByConcreteIds(array $concreteIdsTimestampMap = []): array
    {
        $productAbstractIdTimestampMap = [];

        /** @var \Orm\Zed\Product\Persistence\SpyProductQuery $productQuery */
        $productQuery = $this->getFactory()
            ->getProductQuery()
            ->select([SpyProductTableMap::COL_FK_PRODUCT_ABSTRACT, SpyProductTableMap::COL_ID_PRODUCT]);

        /** @var \Propel\Runtime\Collection\ArrayCollection $productAbstractData */
        $productAbstractData = $productQuery
            ->filterByIdProduct_In(array_keys($concreteIdsTimestampMap))
            ->find()
            ->getData();

        foreach ($productAbstractData as $productAbstract) {
            $productAbstractIdTimestampMap[(int)$productAbstract[SpyProductTableMap::COL_FK_PRODUCT_ABSTRACT]] =
                $concreteIdsTimestampMap[$productAbstract[SpyProductTableMap::COL_ID_PRODUCT]];
        }

        return $productAbstractIdTimestampMap;
    }

    /**
     * @param int $idCategory
     *
     * @return array<int>
     */
    public function getProductListWhitelistIdsByIdCategory(int $idCategory): array
    {
        $productListIdsFromProducts = SpyProductCategoryQuery::create()
            ->filterByFkCategory($idCategory)
            ->useSpyProductAbstractQuery()
                ->useSpyProductQuery()
                    ->joinSpyProductListProductConcrete()
                ->endUse()
            ->endUse()
            ->select(SpyProductListProductConcreteTableMap::COL_FK_PRODUCT_LIST)
            ->distinct()
            ->find()
            ->getData();

        $productListIdsFromCategory = SpyProductListCategoryQuery::create()
            ->filterByFkCategory($idCategory)
            ->select(SpyProductListCategoryTableMap::COL_FK_PRODUCT_LIST)
            ->distinct()
            ->find()
            ->getData();

        $mergedProductListIds = array_unique(array_merge($productListIdsFromProducts, $productListIdsFromCategory));

        return array_map('intval', $mergedProductListIds);
    }

    /**
     * @param array<int> $productConcreteIds
     *
     * @return array<int>
     */
    public function getCategoryNodeIdsByProductConcreteIds(array $productConcreteIds): array
    {
        $categoryIds = SpyProductCategoryQuery::create()
            ->useSpyProductAbstractQuery()
                ->useSpyProductQuery()
                    ->filterByIdProduct_In($productConcreteIds)
                ->endUse()
            ->endUse()
            ->select(SpyProductCategoryTableMap::COL_FK_CATEGORY)
            ->distinct()
            ->find()
            ->getData();

        if (!$categoryIds) {
            return [];
        }

        $categoryNodeIds = SpyCategoryNodeQuery::create()
            ->filterByFkCategory_In($categoryIds)
            ->select(SpyCategoryNodeTableMap::COL_ID_CATEGORY_NODE)
            ->find()
            ->getData();

        return array_map('intval', $categoryNodeIds);
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getCategoryNodeIdsByCategoryIds(array $categoryIds): array
    {
        $categoryNodeIds = SpyCategoryNodeQuery::create()
            ->filterByFkCategory_In($categoryIds)
            ->select(SpyCategoryNodeTableMap::COL_ID_CATEGORY_NODE)
            ->find()
            ->getData();

        return array_map('intval', $categoryNodeIds);
    }
}
