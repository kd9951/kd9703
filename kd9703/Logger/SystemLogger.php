<?php
namespace Kd9703\Logger;

use Carbon\Carbon;
use Crawler\HttpClientInterface;
use Kd9703\Constants\LogLevel;
use Kd9703\Eloquents\Support\SystemLog;
use Kd9703\Entities\Worker\Job;
use Kd9703\Logger\Interfaces\SystemLogger as SystemLoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * システム用ログ
 */
class SystemLogger implements SystemLoggerInterface
{
    use LoggerTrait;

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

    /**
     * @param $level
     * @param $message
     * @param array      $context
     */
    public function log($level, $message, array $context = [])
    {
        $model = new SystemLog();

        $model->level   = $level;
        $model->message = $message;
        $model->context = json_encode($context);

        $model->job_id     = static::$job ? static::$job->job_id : null;
        $model->account_id = static::$job ? static::$job->account_id : null;

        $model->instance    = gethostname();
        $model->remote_addr = isset($_SERVER['REMOTE_ADDR']) ? ip2long($_SERVER['REMOTE_ADDR']) : null;
        $model->user_agent  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $model->created_at  = Carbon::now();

        $model->pid = getmypid();

        $model->save();

        $ratio = env('DB_LOG_FLUSH_RATIO', 100);
        if (rand(0, $ratio - 1) == 0) {
            $this->deleteOld();
        }
    }

    protected function deleteOld()
    {
        $limit = env('DB_LOG_PRESERVE_DAYS', 30);
        $date  = Carbon::parse("-$limit days")->format('Y-m-d H:i:s');
        SystemLog::where('created_at', '<', $date)->delete();
    }
}
