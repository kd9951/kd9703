<?php
namespace Crawler\Parsers;

use Crawler\Exceptions\Parser\PatternNotMatchedException;
use Crawler\Exceptions\Parser\ValidationException;
use Crawler\ParserInterface;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Json のラッパ
 * DomCrawlerと構造を共通化するためのもので
 * わざわざラッピングするほど高度な内容じゃない
 */
class Json implements ParserInterface
{
    use Concerns\PostProcess;

    /**
     * @var Symfony\Component\DomCrawler\Crawler
     */
    protected $json;

    /**
     * コンストラクタにJsonを受け取…れない
     *
     * @param array $json
     */
    public function __construct()
    {
        // $this->json = $json;
    }

    /**
     * 後で代入する
     *
     * @param array $json
     */
    public function set($json)
    {
        $json = json_decode(json_encode($json));

        $this->json = $json;
    }

    ///////////////////////////////////////////////////////////////////////////////
    // 内部ヘルパ だいたいこれで事足りる

    /**
     * @param string $dotIndexer
     */
    private function getByDot(string $dotIndexer): ?array
    {
        return Arr::has($this->json, $dotIndexer) ? Arr::get($this->json, $dotIndexer) : null;
    }

    /**
     * @param string $dotIndexer
     */
    private function hasByDot(string $dotIndexer): bool
    {
        return Arr::has($this->json, $dotIndexer);
    }

    ///////////////////////////////////////////////////////////////////////////////
    // コンテンツクローリング・CSSフィルタリング

    /**
     * フィルタ対象の要素数
     * @return mixed
     */
    public function count(string $dotIndexer): int
    {
        return count($this->getByDot($dotIndexer) ?? []);
    }

    // // /**
    // //  * フィルタで絞り込んでHTMLを取得
    // //  * @return mixed
    // //  */
    // // public function filterHtml(string $cssSelector): string
    // // {
    // //     return $this->crawler ? $this->crawler->filter($cssSelector)->html() : '';
    // // }

    /**
     * いくつかの繰り返されたブロックからパターン抽出
     *
     * @param  array $patterns   'key'=>[pattern],...
     * @param  array $validation 'key'=>'rules',...
     * @return array $data ['key'=>$value,...],['key'=>$value,...],...
     */
    public function fetchManyPattern(string $dotIndexer, array $patterns, array $validation): array
    {
        $baseArray = ($dotIndexer) ? $this->getByDot($dotIndexer) : $this->crawler;
        if ($baseArray === null) {
            throw new PatternNotMatchedException("ベースパターン[$dotIndexer]が見つかりません");
        }

        $return = [];
        foreach ($baseArray ?? [] as $base) {
            $return[] = $this->fetchPatternInternal($patterns, $validation, $base);
        }

        return $return;
    }

    /**
     * 全体からパターン抽出
     *
     * @param  array $patterns   'key'=>[pattern],...
     * @param  array $validation 'key'=>'rules',...
     * @return array $data 'key'=>$value,...
     */
    public function fetchPattern(array $patterns, array $validation): array
    {
        return $this->fetchPatternInternal($patterns, $validation, null);
    }

    /**
     * 全体に１つしか無いブロックからパターン抽出
     *
     * @param  array $patterns   'key'=>[pattern],...
     * @param  array $validation 'key'=>'rules',...
     * @return array $data 'key'=>$value,...
     */
    protected function fetchPatternInternal(array $patterns, array $validation, ?array $baseArray = null): array
    {
        // デフォルトは全体
        $baseArray = $baseArray ?? $this->json;

        $result = [];
        foreach ($patterns as $key => $pattern) {
            $result[$key] = $this->fetchArray($baseArray, $pattern);
        }

        $validator = \Validator::make($result, $validation);
        if ($validator->fails()) {
            throw new ValidationException(
                "バリデーションに失敗"
                . "\nERRS :" . var_export($validator->errors(), true)
                . "\nATTRS:" . var_export($result, true)
                . "\nRULES:" . var_export($validation, true)
            );
        }

        return $result;
    }

    /**
     * パターン指定ノードからパターンにマッチする値を一つ取り出す
     *
     * 有効なメソッド text() attr('attr_name')
     *
     * 有効なオプション
     *   after        => function($value){ return $value; } ポストプロセッサ
     *   absolute_url => http://absolute.com 与えられたベースURLをもとに絶対パスに変換
     *   regex        => /()/ 括弧で与えられたパターンにする マッチしないとパースエラー
     *
     * @param  Crawler  $node
     * @param  array    $pattern ['dot','method','arg','options']
     * @return mixed
     */
    protected function fetchArray(array $baseArray, $pattern)
    {
        if (is_array($pattern)) {
            $selector = $pattern[0];
            $options  = $pattern[1] ?? [];
        } else {
            $selector = $pattern;
            $options  = [];
        }

        if (!$selector) {
            throw new \LogicException('パターンにはセレクターが必要');
        }

        $value = Arr::get($baseArray, $selector);

        if (!empty($options)) {
            $value = $this->postProcess($value, $options);
        }

        return $value;
    }

}
