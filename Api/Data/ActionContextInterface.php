<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Api\Data;

interface ActionContextInterface
{
    public function getPerformedFrom(): string;

    public function getPerformedBy(): string;

    public function getParameters(): array;
}
