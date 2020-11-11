<?php

namespace Crawler\Parsers\Concerns;

use GuzzleHttp\Psr7\Uri;

/**
 * Json のラッパ
 * DomCrawlerと構造を共通化するためのもので
 * わざわざラッピングするほど高度な内容じゃない
 * 必要なメソッドのみ実装する
 */
trait PostProcess
{
    /**
     * オプションに応じて結果を処理する
     *
     * @param  string|null   $value
     * @param  array         $options
     * @return string|null
     */
    protected function postProcess(?string $value, array $options): ?string
    {
        foreach ($options as $method => $arg) {
            // 引数の要らないメソッドは 'trim'=>xxx ではなく 'trim' と書ける
            if (is_numeric($method) && is_string($arg)) {
                $method = $arg;
            }

            switch ($method) {
                // クロージャー
                case 'after':
                    $value = $arg($value);
                    break;

                // トリミング
                case 'trim':
                    $value = trim($value);
                    break;

                // 絶対パス変換
                // @link https://stackoverflow.com/questions/40830285/join-urls-in-symfony-goutte
                case 'absolute_url':
                    $base = new Uri($arg);
                    $value = (string) Uri::resolve($base, $value);
                    break;

                // 正規表現パターン
                case 'regex':
                    if (!preg_match($arg, $value, $m) || !isset($m[1])) {
                        $m[1] = null;
                        // throw new PatternNotMatchedException("正規表現パターン[$value]->[$arg]が見つかりません");
                    }
                    $value = $m[1];
                    break;

                // Y-m-d H:i:s に変換
                case 'datetime':
                    $value = date('Y-m-d H:i:s', $value);
                    break;
            }
        }

        return $value;
    }
}
