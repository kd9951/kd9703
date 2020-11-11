<?php
namespace Kd9703\Entities\Paginate;

use Kd9703\Entities\Entity;
use LogicException;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;

/**
 * ページネーションの出力フォーマット共通化のためのOutputDataオブジェクト
 * LaravelのLengthAwarePaginatorに似ているが「偶然」である
 */
class Output extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'total'        => ['integer', null], // 総アイテム数
        'per_page'     => ['integer', null], // 1ページのアイテム数
        'current_page' => ['integer', null], // 現在のページ １～last_page
        'prev_page'    => ['integer', null], // 前のページ なければNULL
        'next_page'    => ['integer', null], // 次のページ なければNULL
        'last_page'    => ['integer', null], // 最後のページ（最大のページ番号）
        'from'         => ['integer', null], // 最初のアイテム 1～total
        'to'           => ['integer', null], // 最後のアイテム 1～total
    ];

    /**
     * @param mix $input
     */
    public function __construct($input)
    {
        parent::__construct([]);

        if ($input instanceof LengthAwarePaginator) {
            $this->setByLaravelEloquent($input);
            return;
        }
        if ($input instanceof Collection) {
            $this->setByLaravelCollection($input);
            return;
        }
        if (is_array($input)) {
            parent::__construct($input);
            return;
        }

        throw new LogicException('Cant Understand Paginate Format');
    }

    /**
     * paginate で取得した標準ページネータ
     * @param $collection
     */
    public function setByLaravelEloquent($collection)
    {
        $this->total        = $collection->total();
        $this->per_page     = $collection->perPage();
        $this->current_page = $collection->currentPage();
        $this->last_page    = $collection->lastPage();
        $this->prev_page    = $this->current_page <= 1 ? null : $this->current_page - 1;
        $this->next_page    = $this->current_page == $this->last_page ? null : $this->current_page + 1;
        $this->from         = $collection->firstItem();
        $this->to           = $collection->lastItem();
    }

    /**
     * get で全部取得した前提のコレクション 総件数しか無い
     * @param $collection
     */
    public function setByLaravelCollection($collection)
    {
        $this->total        = $collection->count();
        $this->per_page     = null;
        $this->current_page = 1;
        $this->last_page    = 1;
        $this->prev_page    = null;
        $this->next_page    = null;
        $this->from         = 1;
        $this->to           = $this->total;
    }
}
