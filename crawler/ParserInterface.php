<?php
namespace Crawler;


/**
 * パーサーの基本機能
 */
interface ParserInterface
{
    /**
     * 後で代入する
     */
    public function set($subject);

    //////////////////////////////////////////////////////////////////

    /**
     * フィルタ対象の要素数
     * @return mixed
     */
    public function count(string $stringPattern): int;

    /**
     * いくつかの繰り返されたブロックからパターン抽出
     *
     * @param  array $patterns   'key'=>[pattern],...
     * @param  array $validation 'key'=>'rules',...
     * @return array $data ['key'=>$value,...],['key'=>$value,...],...
     */
    public function fetchManyPattern(string $stringPattern, array $patterns, array $validation): array;

    /**
     * 全体からパターン抽出
     *
     * @param  array $patterns   'key'=>[pattern],...
     * @param  array $validation 'key'=>'rules',...
     * @return array $data 'key'=>$value,...
     */
    public function fetchPattern(array $patterns, array $validation): array;
}
