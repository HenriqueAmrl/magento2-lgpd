<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize\Anonymizer;

use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;

final class Phone implements AnonymizerInterface
{
    private const PHONE_NUMBER = '9999999999';

    public function anonymize($value): ?string
    {
        return $value ? self::PHONE_NUMBER : null;
    }
}
