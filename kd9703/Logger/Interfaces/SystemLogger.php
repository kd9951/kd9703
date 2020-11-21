<?php
namespace Kd9703\Logger\Interfaces;

use Crawler\HttpClientInterface;
use Kd9703\Entities\Worker\Job;
use Psr\Log\LoggerInterface;

/**
 * システム用ログ
 */
interface SystemLogger extends LoggerInterface
{
    /**
     * ジョブ開始を記録
     */
    public function startJob(Job $job, int $serial);

    /**
     * ジョブ終了を記録
     */
    public function endJob(Job $job);

    /**
     * MediaAccessが外部アクセスした記録
     * ホストや回数を集計するための重要な指標
     *
     * @param $method
     * @param $url
     * @param $param
     */
    public function mediaCall($method, $url, $getparam, $postapram);

    /**
     * MediaAccessが外部アクセスした際のレスポンス
     * 主にデバッグ用
     * $method, $url, $getparam の組み合わせで mediaCall とペアにする
     *
     * @param $method
     * @param $url
     * @param $param
     */
    public function mediaResponse($method, $url, $getparam, $postapram, HttpClientInterface $client);

    /**
     * 指標を記録
     *
     * @param $method
     * @param $url
     * @param $param
     */
    public function kpi(string $name, string $value, $context = []);

}
