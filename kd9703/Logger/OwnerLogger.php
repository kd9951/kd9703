<?php
namespace Kd9703\Logger;

use Carbon\Carbon;
use Kd9703\Constants\LogLevel;
use Kd9703\Eloquents\Support\OwnerLog;
use Kd9703\Entities\Worker\Job;
use Kd9703\Logger\Interfaces\OwnerLogger as OwnerLoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * オーナー用ログ 各ユーザーに通知される
 */
class OwnerLogger implements OwnerLoggerInterface
{
    use LoggerTrait;

    // ▽ 全オーナー
    // EMERGENCY
    // ALERT
    // CRITICAL システム側の都合で機能不全 復旧を待て

    // ▽ ログイン中オーナーのみ
    // ERROR    処理中断 設定ミス 何かしら設定変更を促す
    // WARNING  一部失敗 設定ミスの可能性 変更すれば改善するかも
    // NOTICE   通常の結果／機能不十分 フォロータグ不足などで十分な数をスクレピングできなかった
    // INFO     細かい結果
    // DEBUG    なし

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
     * @param $level
     * @param $message
     * @param array      $context
     */
    public function log($level, $message, array $context = [])
    {
        if (!static::$job) {
            throw new \LogicException("Job must be set." . var_export(compact('level', 'message', 'context'), true));
        }

        $model = new OwnerLog();

        $model->level   = $level;
        $model->message = $message;
        $model->context = json_encode($context);

        $model->job_id     = static::$job->job_id;
        $model->account_id = static::$job->account_id;

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

        $date    = date('Y-m-d H:i:s');
        $message = preg_replace('/\s+/', ' ', $message);
        echo "$date OWNER [$level] $message\n";
    }

    protected function deleteOld()
    {
        $limit = env('DB_LOG_PRESERVE_DAYS', 30);
        $date  = Carbon::parse("-$limit days")->format('Y-m-d H:i:s');
        OwnerLog::where('created_at', '<', $date)->delete();
    }
}
