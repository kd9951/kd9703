<?php
namespace Kd9703\Resources\Interfaces;

/**
 * タグ
 */
interface Transaction
{
    /**
     * トランザクション開始
     */
    public function beginTransaction(): void;

    /**
     * ロールバック
     */
    public function rollback(): void;

    /**
     * コミット
     */
    public function commit(): void;
}
