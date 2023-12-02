<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize\Anonymizer;

use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;

final class NullValue implements AnonymizerInterface
{
    public function anonymize($value)
    {
        return null;
    }
}
