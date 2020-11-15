<?php
namespace Kd9703\MediaAccess\Twitter;

use Carbon\Carbon;
use Crawler\Exceptions\Parser\PatternNotMatchedException;
use Kd9703\Constants\Media;
use Kd9703\Constants\Prefecture;
use Kd9703\Entities\Media\Account;
use Kd9703\MediaAccess\Interfaces\GetProfile as GetProfileInterface;

/**
 * 通知を取得
 */
class GetProfile extends MediaAccess implements GetProfileInterface
{
    const ENDPOINT_USER   = '/users/lookup'; // :account_id

    /**
     * @param  Account $account
     * @param  Account $target_account
     * @return mixed
     */
    public function exec(Account $account, Account $target_account): Account
    {
        $this->wait->waitNormal('twitter.getProfile', 500, 1500);

        $url = self::ENDPOINT_USER;
        $this->system_logger->mediaCall('GET', $url, [], [], $account);
        $this->client->get($url, ['user_id' => $target_account->account_id]);
        $this->system_logger->mediaResponse('GET', $url, [], [], $this->client, $account);

        $parsed    = $this->parseUser($this->client->getContentAs('json.array'));
        $formatted = $this->format($parsed);

        foreach ($formatted as $key => $value) {
            if (!is_null($value)) {
                $target_account->$key = $value;
            }
        }

        $target_account->reviewed_at = Carbon::now();

        return $target_account;
    }

    /**
     * HTMLからパターン抽出
     *
     * @return mixed
     */
    protected function parseUser($crawler)
    {
        $this->parser->set($crawler);

        return $this->parser->fetchPattern([
            'account_id'       => ['meta[property="og:url"]', 'attr', 'content', ['regex' => '/user\/([0-9]+)/']],
            'username'         => ['.My__InfoName', 'text', null, ['regex' => '/\(@(.+)\)/']],
            'fullname'         => ['title', 'text', null, ['regex' => '/^(.+)のアルバム/']],
            'prefecture'       => ['.My__InfoAddress', 'text', null],
            'description'      => ['meta[name="description"]', 'attr', 'content'],
            'web_url1'         => ['.My__InfoEtc | eq(0)', 'text', null],
            'web_url2'         => ['.My__InfoEtc | eq(1)', 'text', null],
            'web_url3'         => ['.My__InfoEtc | eq(2)', 'text', null],
            'img_thumnail_url' => ['.My__BackgroundPrfImg img', 'attr', 'src', ['absolute_url' => self::ENDPOINT_MYPAGE]],
            'img_cover_url'    => ['.My__Background img', 'attr', 'src', ['absolute_url' => self::ENDPOINT_MYPAGE]],
            // 'score'            => ['', 'text', null],
            'count_posts'      => ['#postCount', 'text', null],
            'count_follow'     => ['#followCount', 'text', null],
            'count_follower'   => ['#followerCount', 'text', null],
            // 'last_posted_at'   => ['', 'text', null],
        ], [
            'account_id'       => 'required|string',
            'username'         => 'nullable|string', // セットしていないことも多い
            'fullname'         => 'required|string',
            'prefecture'       => 'nullable|string',
            'description'      => 'required|string',
            'web_url1'         => 'nullable|string',
            'web_url2'         => 'nullable|string',
            'web_url3'         => 'nullable|string',
            'img_thumnail_url' => 'required|url',
            'img_cover_url'    => 'nullable|url',
            'count_posts'      => 'required|integer',
            'count_follow'     => 'required|integer',
            'count_follower'   => 'required|integer',
        ]);
    }

    /**
     * 抽出したパターンモデルに合わせる
     *
     * @return mixed
     */
    protected function format(array $result)
    {
        // 都道府県が漢字なのでIDに変換
        if (array_key_exists('prefecture', $result) && !empty($result['prefecture'])) {
            $key                  = array_search($result['prefecture'], Prefecture::TEXT_JPN);
            $result['prefecture'] = ($key !== false) ? $key : null;
            if ($key === false) {
                throw new PatternNotMatchedException("都道府県としてピックした {$result['prefecture']} は都道府県リストにありません。");
            }
        }

        $formatted = [
            'media'            => Media::GREEN_SNAP(),
            'account_id'       => $result['account_id'],
            'fullname'         => $result['fullname'],
            // 'login_method'     => null,
            // 'login_id'         => null,
            // 'password'         => null,
            'img_thumnail_url' => $result['img_thumnail_url'],
            // 'score'            => 0,
            'total_post'       => (int) $result['count_posts'],
            'total_follow'     => (int) $result['count_follow'],
            'total_follower'   => (int) $result['count_follower'],
            // 'last_posted_at'   => ,
            // 'total_likes'      => ,
            'is_private'       => false,
        ];

        foreach ([
            'username',
            'prefecture',
            'web_url1',
            'web_url2',
            'web_url3',
            'img_cover_url',
        ] as $key) {
            if (!empty($result[$key])) {
                $formatted[$key] = $result[$key];
            }
        }

        return $formatted;
    }

}
