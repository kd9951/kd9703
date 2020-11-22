<?php
namespace Kd9703\Framework\StrictInvokator;

/**
 * 繰り返し実行すると前回の続きから実行されることが前提の
 * 前回中断したところから再開することができるユースケース
 * 主に大量のデータをページネーションできるMediaAccessのハンドリング（アダプタ）
 */
interface Suspendable extends StrictInvokatorInterface
{
    /**
     * execして結果が返ってきた後に
     * 途中で止まったか？を確認する
     *
     * @return boolean
     */
    public function isSuspended(): bool;

    /**
     * 途中で止まっていた場合、再開用の文字列を返す
     *
     * @return string
     */
    public function getNextCursor(): string;

    /**
     * 次回その文字列をセットしてからexecすると再開できる
     * （もちろんexecの引数は前回と同じ）
     *
     * @param  string $next_cursor
     * @return void
     */
    public function setNextCursor(string $next_cursor): void;
}
