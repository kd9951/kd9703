<?php
namespace Crawler\HttpClients;

// require_once __DIR__ . '/UltimateOAuth.php';

use Crawler\HttpClientInterface;
use mpyw\Cowitter\Client;
use mpyw\Cowitter\HttpException;
use mpyw\Cowitter\Response;

/**
 * Cowitterを利用したTwitterAPI
 * アクセスURLは相対パス
 */
class TwitterApi implements HttpClientInterface
{
    /**
     * @var mixed
     */
    protected $twitter_id = null;
    /**
     * @var mixed
     */
    protected $consumer_key = null;
    /**
     * @var mixed
     */
    protected $consumer_secret = null;
    /**
     * @var mixed
     */
    protected $access_token = null;
    /**
     * @var mixed
     */
    protected $access_token_secret = null;

    /**
     * @var Client
     */
    protected $client = null;

    /**
     * @var string
     */
    protected $response_raw_body = null;

    /**
     * @var array
     */
    protected $response_body = null;

    /**
     * @var int
     */
    protected $response_code = null;

    /**
     * @param string     $twitter_id
     * @param nullstring $consumer_key
     * @param nullstring $consumer_secret
     * @param nullstring $access_token
     * @param nullstring $access_token_secret
     */
    public function __construct(
        string $twitter_id = null,
        string $consumer_key = null,
        string $consumer_secret = null,
        string $access_token = null,
        string $access_token_secret = null
    ) {
        if ($twitter_id) {
            $this->setToken(
                $twitter_id,
                $consumer_key,
                $consumer_secret,
                $access_token,
                $access_token_secret
            );
        }
    }

    /**
     * @param string $twitter_id
     * @param string $consumer_key
     * @param string $consumer_secret
     * @param string $access_token
     * @param string $access_token_secret
     */
    public function setToken(
        string $twitter_id,
        string $consumer_key,
        string $consumer_secret,
        string $access_token,
        string $access_token_secret
    ) {
        $this->twitter_id          = $twitter_id;
        $this->consumer_key        = $consumer_key;
        $this->consumer_secret     = $consumer_secret;
        $this->access_token        = $access_token;
        $this->access_token_secret = $access_token_secret;

        $this->setLogInId($twitter_id);
        // dd([
        //     $consumer_key,
        //     $consumer_secret,
        //     $access_token,
        //     $access_token_secret,
        // ]);
        $this->client = new Client([
            $consumer_key,
            $consumer_secret,
            $access_token,
            $access_token_secret,
        ]);
    }

    /**
     * ユーザーエージェントを切り替える
     *
     * @param  string     $platform
     * @return MyClient
     */
    public function setUserAgent(string $platform): HttpClientInterface
    {
        return $this;
    }

    ///////////////////////////////////////////////////////////////////////////////
    // HTTPアクセス

    /**
     * 指定されたURLを開く
     * 結果がうまくいったかどうかは getResponseStatusCode で判断する
     *
     * @param string $url
     */
    public function get(string $url, array $getparam = []): void
    {
        if (!$this->client) {
            throw new \LogicException('Twitter Client has not been initialized.');
        }

        $this->url               = $url;
        $this->response_raw_body = null;
        $this->response_body     = null;
        $this->response_code     = null;

        try {
            $response = $this->client->get($url, $getparam, true);

            $this->response_raw_body = $response->getRawContent();
            $this->response_body     = $response->getContent();
            $this->response_code     = $response->getStatusCode();

        } catch (HttpException $e) {

            $this->response_raw_body = $e->getMessage();
            $this->response_body     = $e->getMessage();
            $this->response_code     = $e->getStatusCode();

            if ($this->response_code === -1) {
                $this->response_code = $e->getCode();
            }

        } catch (\RuntimeException $e) {

            $this->response_raw_body = $e->getMessage();
            $this->response_body     = $e->getMessage();
            $this->response_code     = $e->getCode();
        }
    }

    /**
     * 指定されたURLへPOST送信
     * POSTだけどGETパラメータが指定可能
     * POSTでログインするときは、その後で setLoginId を実行してインスタンスをログイン状態にする
     * 結果がうまくいったかどうかは getResponseStatusCode で判断する
     *
     * @param string $url
     */
    public function post(string $url, array $getparam = [], array $param = []): void
    {
        if (!$this->client) {
            throw new \LogicException('Twitter Client has not been initialized.');
        }

        // UltimateOAuthRotateで回避できないかと試行錯誤した跡
        // $uo = new UltimateOAuthRotate();
        // // $uo = new UltimateOAuth(
        // //     $this->consumer_key,
        // //     $this->consumer_secret,
        // //     $this->access_token,
        // //     $this->access_token_secret
        // // );
        // $uo->register(
        //     'owner',
        //     $this->consumer_key,
        //     $this->consumer_secret,
        //     $this->access_token,
        //     $this->access_token_secret
        // );
        // $result = $uo->login('kd9951@gmail.com', 'Hmotspr7', true);
        // dd($result);
        // $response = $uo->post($url, $getparam);
        // // dd($response);
        // $this->response_body = $response;
        // $this->response_code = 200;
        // return;

        $this->url               = $url;
        $this->response_raw_body = null;
        $this->response_body     = null;
        $this->response_code     = null;

        try {
            $response = $this->client->post($url, array_merge($getparam, $param), true);

            $this->response_raw_body = $response->getRawContent();
            $this->response_body     = $response->getContent();
            $this->response_code     = $response->getStatusCode();

        } catch (HttpException $e) {

            $this->response_raw_body = $e->getMessage();
            $this->response_body     = $e->getMessage();
            $this->response_code     = $e->getStatusCode();

            if ($this->response_code === -1) {
                $this->response_code = $e->getCode();
            }

        } catch (\RuntimeException $e) {

            $this->response_raw_body = $e->getMessage();
            $this->response_body     = $e->getMessage();
            $this->response_code     = $e->getCode();
        }

    }

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
    public function formLogin(string $url, string $form_selector, string $id_name, string $id, string $pw_name, string $pw): void
    {
        throw new \LogicException('not implemented');
    }

    /**
     * インスタンスがログイン状態か？
     *
     * @return bool $result 成功／失敗
     */
    public function isLoggedIn(): bool
    {
        return $this->twitter_id;
    }

    /**
     * クライアントを「ログインされた」状態にする
     *
     * @param string $url
     */
    public function setLogInId($id): void
    {
        $this->twitter_id = $id;
    }

    ///////////////////////////////////////////////////////////////////////////////
    // 結果確認

    /**
     * たどり着いたURL
     * @return mixed
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * 最後のレスポンスのステータスコード
     * @return mixed
     */
    public function getResponseStatusCode(): int
    {
        return $this->response_code;
    }

    /**
     * 最後のレスポンスのコンテンツ
     * @return mixed
     */
    public function getContent(): string
    {
        return $this->response_raw_body;
    }

    /**
     * 最後のレスポンスのコンテンツを指定形式で取得
     * @return mixed
     */
    public function getContentAs(string $type)
    {
        switch (strtolower($type)) {
            case 'object':
            case 'json.object':
                return json_decode($this->response_raw_body, false);

            case 'array':
            case 'json.array':
                return json_decode($this->response_raw_body, true);
        }

        throw new \LogicException("Undefined format type:$type");
    }
}
