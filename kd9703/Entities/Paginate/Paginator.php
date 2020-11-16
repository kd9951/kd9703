<?php
namespace Kd9703\Entities\Paginate;

use Kd9703\Entities\Entity;

/**
 * ページネーションボタンをレンダリングするためのパラメータ
 * [<<] [1] ... [3] [4] 5 [6] [7] ... [13] [>>]
 */
class Paginator extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'show_first'         => 'boolean', // [<<] を活性化するか
        'show_forward_dash'  => 'boolean', // ... を表示するか
        'pages_forward'      => 'array of integer', // 現在ページの前に表示するボタン
        'pages_backward'     => 'array of integer', // 後に表示するボタン
        'show_backward_dash' => 'boolean', // ... を表示するか
        'show_last'          => 'boolean', // [>>] を活性化するか
    ];

    /**
     * @param Output $input
     * @param int    $max_buttons
     */
    public function __construct(Output $output, int $max_buttons)
    {
        $this->show_first        = $output->current_page > 1;
        $this->show_forward_dash = $output->current_page - $max_buttons >= 2;

        $start               = max(2, $output->current_page - $max_buttons + 1);
        $end                 = $output->current_page;
        $this->pages_forward = $start < $end ? range($start, $end - 1) : [];

        $this->show_backward_dash = $output->current_page + $max_buttons <= $output->last_page - 1;
        $this->show_last          = $output->current_page < $output->last_page;

        $start                = $output->current_page + 1;
        $end                  = min($output->last_page, $output->current_page + $max_buttons);
        $this->pages_backward = $start < $end ? range($start, $end - 1) : [];
    }

}
