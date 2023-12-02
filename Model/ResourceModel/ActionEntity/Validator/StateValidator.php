<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity\Validator;

use Magento\Framework\Phrase;
use Magento\Framework\Validator\AbstractValidator;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntityInterface;
use HenriqueAmrl\Lgpd\Model\Config\Source\ActionStates;
use function array_column;
use function in_array;

final class StateValidator extends AbstractValidator
{
    private ActionStates $actionStates;

    public function __construct(
        ActionStates $actionStates
    ) {
        $this->actionStates = $actionStates;
    }

    /**
     * @param ActionEntityInterface $actionEntity
     * @return bool
     */
    public function isValid($actionEntity): bool
    {
        $this->_clearMessages();
        $isValid = in_array(
            $actionEntity->getState(),
            array_column($this->actionStates->toOptionArray(), 'value'),
            true
        );

        if (!$isValid) {
            $this->_addMessages([
                'state' => new Phrase('State "%1" does not exists.', [$actionEntity->getState()])
            ]);
        }

        return $isValid;
    }
}
