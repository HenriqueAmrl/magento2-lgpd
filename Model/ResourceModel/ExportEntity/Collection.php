<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\ResourceModel\ExportEntity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;
use HenriqueAmrl\Lgpd\Model\ExportEntity;
use HenriqueAmrl\Lgpd\Model\ResourceModel\ExportEntity as ExportEntityResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ExportEntity::class, ExportEntityResourceModel::class);
        $this->_setIdFieldName(ExportEntityInterface::ID);
    }
}
