<?php
namespace App\Extensions\Logger;

use Crawler\HttpClientInterface;
use Kd9703\Constants\LogLevel;
use Kd9703\Entities\Worker\Job;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Psr\Log\AbstractLogger;

class ConsoleLogger extends AbstractLogger implements SystemLogger, OwnerLogger
{
    /**
     * @var mixed
     */
    static $job = null;
    /**
     * @var mixed
     */
    static $serial = null;
    /**
     * @var mixed
     */
    static $jobtime = null;

    /**
     * @var array
     */
    public $excludes = [];

    /**
     * @param  $level
     * @param  $message
     * @param  array      $context
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        if (in_array($level, $this->excludes)) {
            return;
        }
        $trace = debug_backtrace();

        $line     = $trace[0]['line'] ?? '(line)';
        $file     = $trace[0]['file'] ?? '(file)';
        $function = $trace[1]['function'] ?? '(function)';
        $class    = $trace[1]['class'] ?? '(class)';

        echo sprintf("[%s] %s", strtoupper($level), $message);
        if (!empty($context)) {
            echo ' ';
            echo json_encode($context);
        }

        echo "$file ($line)\n";
        echo "$class @ $function \n";

        echo "\n";
    }

    /**
     * ジョブ開始を記録
     */
    public function startJob(Job $job, int $serial)
    {
        if (static::$job) {
            $current = static::$job;
            throw new \LogicException("Job ($current->job_class for account $current->media/$current->account_id) has already started, thus could not start another job ($job->job_class for account $job->media/$job->account_id).");
        }

        static::$job     = $job;
        static::$serial  = $serial;
        static::$jobtime = microtime(true);

        $job = static::$job;
        $this->log(LogLevel::JOB, "[START JOB] $job->job_class for account $job->media/$job->account_id.");
    }

    /**
     * ジョブ終了を記録
     */
    public function endJob(Job $job)
    {
        $job     = static::$job;
        $milisec = number_format((microtime(true)-static::$jobtime) * 1000);
        $this->log(LogLevel::JOB, "[END JOB] {$milisec}ms $job->job_class for account $job->media/$job->account_id.");

        static::$job     = null;
        static::$serial  = null;
        static::$jobtime = null;
    }

    /**
     * @var array
     */
    static $time = [];

    /**
     * MediaAccessが外部アクセスした記録
     * ホストや回数を集計するための重要な指標
     *
     * @param $method
     * @param $url
     * @param $param
     */
    public function mediaCall($method, $url, $getparam, $postapram)
    {
        $query_string                 = http_build_query($getparam);
        $formatted_url                = "[$method] $url" . ($query_string ? '?' : '') . $query_string;
        static::$time[$formatted_url] = microtime(true);

        $this->log(LogLevel::MEDIA_ACCESS, "\nCALL     $formatted_url");
    }

    /**
     * MediaAccessが外部アクセスした際のレスポンス
     * 主にデバッグ用
     * $method, $url, $getparam の組み合わせで mediaCall とペアにする
     *
     * @param $method
     * @param $url
     * @param $param
     */
    public function mediaResponse($method, $url, $getparam, $postapram, HttpClientInterface $client)
    {
        $query_string  = http_build_query($getparam);
        $formatted_url = "[$method] $url" . ($query_string ? '?' : '') . $query_string;
        $milisec       = (microtime(true)-static::$time[$formatted_url]) * 1000;
        unset(static::$time[$formatted_url]);

        $status_code = $client->getResponseStatusCode();
        $content     = $client->getContent();
        $content     = substr($content, 0, 200) . (strlen($content) > 200 ? '...' : '');
        $content     = preg_replace('/[\n\r]+/', '\\n', $content);
        $milisec     = number_format($milisec);

        $this->log(LogLevel::MEDIA_ACCESS, "RESPONSE {$milisec}ms CODE [$status_code] CONTENT $content");
    }
}
