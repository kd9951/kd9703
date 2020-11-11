<?php

namespace Tests;

trait InteractsWithExecutionTime
{

    /**
     * @var mixed
     */
    private $trackedtime = null;

    /**
     *
     * @param string $message
     */
    protected function trackTime(string $message = '')
    {
        if ($message && $this->trackedtime) {
            $m = microtime(true) - $this->trackedtime;
            echo "\n[TIME] " . sprintf('%6s', number_format($m * 1000)) . "[ms] @ $message\n";
        }
        $this->trackedtime = microtime(true);
    }
    /**
     *
     * @param string $message
     */
    protected function assertTimeLessThan(int $miliseconds)
    {
        $m = microtime(true) - $this->trackedtime;

        $this->assertLessThan($miliseconds, floor($m * 1000), "処理時間がかかりすぎ");
    }
}
