<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Validator\ValidatorInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntityInterface;
use HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity\Validator;

class ActionEntity extends AbstractDb
{
    public const TABLE = 'henriqueamrl_lgpd_action_entity';

    private Validator $validator;

    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $relationComposite,
        Validator $validator,
        ?string $connectionName = null
    ) {
        $this->validator = $validator;
        parent::__construct($context, $entitySnapshot, $relationComposite, $connectionName);
    }

    protected function _construct(): void
    {
        $this->_init(self::TABLE, ActionEntityInterface::ID);
        $this->_serializableFields = [ActionEntityInterface::PARAMETERS => [[], []]];
    }

    public function getValidationRulesBeforeSave(): ValidatorInterface
    {
        return $this->validator;
    }
}
