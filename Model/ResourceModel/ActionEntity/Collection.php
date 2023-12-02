<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntityInterface;
use HenriqueAmrl\Lgpd\Model\ActionEntity;
use HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity as ActionEntityResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ActionEntity::class, ActionEntityResourceModel::class);
        $this->_setIdFieldName(ActionEntityInterface::ID);
    }
}
