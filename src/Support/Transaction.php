<?php

namespace Lucid\Support;

interface Transaction
{
    public function getTransactionConnections(): array;
}
