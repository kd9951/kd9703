<?php
namespace Kd9703\Resources\Kd9703\Tools;

use Illuminate\Database\Eloquent\Builder;

trait SearchKeyword
{
    /**
     * クエリパラメータをそのままDBカラムとして絞り込みする強力な検索メソッド
     * 管理画面以外では使用しないこと
     * sort と order で並び順も指定できる
     *
     * @param  Builder   $query
     * @param  array     $keywords スペース区切りの検索キーワード（ユーザー入力でOK）
     * @param  array     $columns  対象のカラム
     * @return Builder
     */
    protected function searchKeyword(Builder $query, string $keywords, array $columns): Builder
    {
        $keywords = mb_convert_kana($keywords, 'saKV');
        $keywords = preg_replace('/[、，]/u', ',', $keywords);
        $keywords = preg_replace('/[\s;:,]+/', ' ', $keywords);

        if ($keywords) {
            // すべてのキーワードを含む
            foreach (explode(' ', $keywords) as $keyword) {
                $query = $query->where(function ($q) use ($columns, $keyword) {
                    // 指定されたカラムのいずれかに
                    foreach ($columns as $col) {
                        $keyword = str_replace('\\', '\\\\', $keyword);
                        $keyword = str_replace('%', '\\%', $keyword);
                        $keyword = str_replace('_', '\\_', $keyword);
                        $q->orWhere($col, 'like', "%{$keyword}%");
                    }
                });
            }
        }

        return $query;
    }
}
