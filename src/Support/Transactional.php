<?php

namespace Lucid\Support;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Transactional
{
    public function __construct(public array $connections)
    {
    }
}
