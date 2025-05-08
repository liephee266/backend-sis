<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ApiField
{
    public function __construct(
        public bool $auto = false,
        public ?string $description = null
    ) {}
}
