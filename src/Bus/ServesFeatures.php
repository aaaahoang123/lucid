<?php

namespace Lucid\Bus;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use Lucid\Events\FeatureStarted;

trait ServesFeatures
{
    use Marshal;
    use DispatchesJobs;
    use SupportTransaction;

    /**
     * Serve the given feature with the given arguments.
     */
    public function serve(string $feature, array $arguments = []): mixed
    {
        event(new FeatureStarted($feature, $arguments));

        return $this->transaction(
            $this->marshal($feature, new Collection(), $arguments),
            fn($unit) => $this->dispatchSync($unit),
        );
    }
}
