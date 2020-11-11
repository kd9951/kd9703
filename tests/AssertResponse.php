<?php

namespace Tests;

use Carbon\Carbon;

/**
 * レスポンスに対するアサーション
 * 基本はLaravelに備わっているのでサポート的な便利ツール
 */
trait AssertResponse
{
    protected function assert( ): ?SendMail
    {
        return SendMail::orderBy('id','desc')->first();
    }

    /**
     * HTMLレスポンスがエラーの場合、そこからエラーメッセージを抜き出して表示
     *
     * @param [type] $response
     * @param boolean $return
     * @return void
     */
    protected function dumpHtmlError( $response, $return = false )
    {
        if( $response->status() == 200 ){
            return '';
        }

        if( $response->status() == 422 ){
            echo "\n";
            echo "\n";
            echo "----------------------\n";
            echo " VALIDATION ERROR\n";
            echo "----------------------\n";
            var_export( json_decode( $response->getContent(), true ) );
            return '';
        }

        $content = $response->getContent();
        $json = json_decode($content, true);
        if( $json === null ){
            //HTML
            preg_match('/<!--(.*?)-->.*<html>/s',$content,$m);
            if( !isset($m[1]) ){
                return;
            }
    
            $body = htmlspecialchars_decode($m[1],ENT_QUOTES|ENT_HTML5);
            $body = trim( $body );
    
            if( $body ){
                echo "\n";
                echo "\n";
                echo "----------------------\n";
                echo " SYMFONY DEBUG REPORT\n";
                echo "----------------------\n";
                echo $body;
                echo "\n";
                echo "----------------------\n";
            }            
        } else {
            // JSON
            if( !isset($json['message']) ){
                return "";
            }
            echo "\n";
            echo "\n";
            echo "----------------------\n";
            echo "  JSON DEBUG REPORT\n";
            echo "----------------------\n";
            echo $json['message'] . "\n";
            echo $json['exception'] . "\n";
            echo "{$json['file']}({$json['line']})\n";
            echo "----------------------\n";
        };
    }
}
