<?php

namespace Lucid\Bus;

use Closure;
use Illuminate\Support\Facades\DB;
use Lucid\Support\Transaction;
use Lucid\Support\Transactional;
use ReflectionClass;
use Throwable;

trait SupportTransaction {
    public function transaction($unit, Closure $callback = null)
    {
        $transactionsConnections = $this->detectTransactionalFromUnit($unit);

        try {
            foreach ($transactionsConnections as $connection) {
                DB::connection($connection)->beginTransaction();
            }
            $result = $callback($unit);

            foreach ($transactionsConnections as $connection) {
                DB::connection($connection)->commit();
            }

            return $result;
        } catch (Throwable $exception) {
            foreach ($transactionsConnections as $connection) {
                DB::connection($connection)->rollBack();
            }

            throw $exception;
        }
    }

    public function detectTransactionalFromUnit($unit)
    {
        if ($unit instanceof Transaction) {
            return $unit->getTransactionConnections();
        }

        $ref = new ReflectionClass($unit);

        $attributes = $ref->getAttributes(Transactional::class);

        $connections = [];

        foreach ($attributes as $attribute) {
            /** @var Transactional $instance */
            $instance = $attribute->newInstance();

            $connections = array_merge($connections, $instance->connections);
        }

        return $connections;
    }
}
