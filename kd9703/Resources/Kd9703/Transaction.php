<?php
namespace Kd9703\Resources\Kd9703;

use Kd9703\Resources\Interfaces\Transaction as TransactionInterface;
use Illuminate\Database\ConnectionInterface;

/**
 * タグ
 */
class Transaction implements TransactionInterface
{
    /**
     * @var ConnectionInterface
     */private $db;

    /**
     * @param ConnectionInterface $db
     */
    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    /**
     * トランザクション開始
     */
    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    /**
     * ロールバック
     */
    public function rollback(): void
    {
        $this->db->rollback();
    }

    /**
     * コミット
     */
    public function commit(): void
    {
        $this->db->commit();
    }
}
