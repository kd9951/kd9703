<?php
namespace Kd9703\Entities\Paginate;

use Kd9703\Entities\Entity;

/**
 * ページネータに指示を出すための入力フォーマット共通化のためのInputDataオブジェクト
 * LaravelのLengthAwarePaginatorに似ているが「偶然」である
 */
class Input extends Entity
{
    // ページネーションせず全件取得する
    const ALL = null;

    /**
     * @var array
     */
    protected $attritubes = [
        'per_page' => ['?int', null], // 1ページのアイテム数 nullはすべて
        'page'     => ['int', null], // 現在のページ １～last_page
    ];
}
