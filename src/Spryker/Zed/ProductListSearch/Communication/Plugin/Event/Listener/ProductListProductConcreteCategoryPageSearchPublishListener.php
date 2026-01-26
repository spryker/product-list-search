<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Communication\Plugin\Event\Listener;

use ArrayObject;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\HydrateEventsRequestTransfer;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListProductConcreteTableMap;
use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\ProductListSearch\Communication\ProductListSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductListSearch\Business\ProductListSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductListSearch\ProductListSearchConfig getConfig()
 * @method \Spryker\Zed\ProductListSearch\Persistence\ProductListSearchRepositoryInterface getRepository()
 */
class ProductListProductConcreteCategoryPageSearchPublishListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName): void
    {
        $hydrateEventsResponseTransfer = $this->getFactory()->getEventBehaviorFacade()->hydrateEventDataTransfer(
            (new HydrateEventsRequestTransfer())
                ->setEventEntities(new ArrayObject($eventEntityTransfers))
                ->setForeignKeyName(SpyProductListProductConcreteTableMap::COL_FK_PRODUCT),
        );

        if (count($hydrateEventsResponseTransfer->getForeignKeyTimestampMap()) === 0) {
            return;
        }

        $categoryNodeIds = $this->getRepository()
            ->getCategoryNodeIdsByProductConcreteIds(array_keys($hydrateEventsResponseTransfer->getForeignKeyTimestampMap()));

        if (!$categoryNodeIds) {
            return;
        }

        $categoryNodeEventEntityTransfers = [];

        foreach ($categoryNodeIds as $categoryNodeId) {
            $categoryNodeEventEntityTransfers[] = (new EventEntityTransfer())->setId($categoryNodeId);
        }

        $this->getFactory()
            ->getCategoryPageSearchFacade()
            ->writeCategoryNodePageSearchCollectionByCategoryNodeEvents($categoryNodeEventEntityTransfers);
    }
}
