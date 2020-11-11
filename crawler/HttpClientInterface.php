<?php
namespace Crawler;

/**
 * HttpClient の共通基本機能
 * WEBだろうが、JSONだろうが…
 */
interface HttpClientInterface
{
    /**
     * ユーザーエージェントを切り替える
     *
     * @param  string     $platform
     * @return MyClient
     */
    public function setUserAgent(string $platform): HttpClientInterface;

    ///////////////////////////////////////////////////////////////////////////////
    // HTTPアクセス

    /**
     * 指定されたURLを開く
     * 結果がうまくいったかどうかは getResponseStatusCode で判断する
     *
     * @param string $url
     */
    public function get(string $url, array $getparam = []): void;

    /**
     * 指定されたURLへPOST送信
     * POSTだけどGETパラメータが指定可能
     * POSTでログインするときは、その後で setLoginId を実行してインスタンスをログイン状態にする
     * 結果がうまくいったかどうかは getResponseStatusCode で判断する
     *
     * @param string $url
     */
    public function post(string $url, array $getparam = [], array $param = []): void;

    /**
     * 指定されたURLにあるフォーム要素にID、PWを入力してログインを試みる
     * Goutteのマクロ
     * 結果がうまくいったかどうかは getResponseStatusCode で判断する
     *
     * @param string $url
     * @param string $form_selector
     * @param string $id_name
     * @param string $id
     * @param string $pw_name
     * @param string $pw
     */
    public function formLogin(string $url, string $form_selector, string $id_name, string $id, string $pw_name, string $pw): void;

    /**
     * インスタンスがログイン状態か？
     *
     * @return bool $result 成功／失敗
     */
    public function isLoggedIn(): bool;

    /**
     * クライアントを「ログインされた」状態にする
     *
     * @param string $url
     */
    public function setLogInId($id): void;

    ///////////////////////////////////////////////////////////////////////////////
    // 結果確認

    /**
     * たどり着いたURL
     * @return mixed
     */
    public function getUrl(): string;

    /**
     * 最後のレスポンスのステータスコード
     * @return mixed
     */
    public function getResponseStatusCode(): int;

    /**
     * 最後のレスポンスのコンテンツ
     * @return mixed
     */
    public function getContent(): string;

    /**
     * 最後のレスポンスのコンテンツを指定形式で取得
     * @return mixed
     */
    public function getContentAs(string $type);
}
