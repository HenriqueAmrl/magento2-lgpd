<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\ResourceModel\EraseEntity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;
use HenriqueAmrl\Lgpd\Model\EraseEntity;
use HenriqueAmrl\Lgpd\Model\ResourceModel\EraseEntity as EraseEntityResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(EraseEntity::class, EraseEntityResourceModel::class);
        $this->_setIdFieldName(EraseEntityInterface::ID);
    }
}
