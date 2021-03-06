<?php
namespace Kd9703\MediaAccess\Twitter;

use Kd9703\Entities\Media\Account;
use Kd9703\MediaAccess\Interfaces\DenyFollowerIncoming as DenyFollowerIncomingInterface;
use Kd9703\MediaAccess\Twitter\Tools\FormatUserObjectForAccount;

/**
 * フォローリクエストを承認
 */
class DenyFollowerIncoming extends MediaAccess implements DenyFollowerIncomingInterface
{
    use FormatUserObjectForAccount;

    const ENDPOINT_ACCEPT = 'https://api.twitter.com/1/friendships/deny.json';

    /**
     * @param  Account $account
     * @param  Account $target_account
     * @return mixed
     */
    public function exec(Account $account, string $target_account_id): ?Account
    {
        $this->wait->waitNormal('twitter.AcceptFollowerIncoming', 200, 800);

        $url   = self::ENDPOINT_ACCEPT;
        $param = [
            'user_id' => $target_account_id,
        ];
        $this->system_logger->mediaCall('POST', $url, $param, [], $account);
        $this->client->post($url, $param);
        $this->system_logger->mediaResponse('POST', $url, $param, [], $this->client, $account);

        $response_json_array = $this->client->getContentAs('json.array');

        if ($this->client->getResponseStatusCode() == 410) {
            // エンドポイントが存在しない Twitter For Mac じゃない一般アプリとして承認したトークン
            $this->system_logger->warning('cannot exec accepting. endpoint not found.', compact('url', 'param', 'response_json_array'));
            return null;
        } elseif (!is_array($response_json_array)) {
            $this->system_logger->error(self::ENDPOINT_ACCEPT . ' returns invalid json.', compact('url', 'param', 'response_json_array'));
            return null;
        } else {
            $formatted = $this->FormatUserObjectForAccount($response_json_array);
        }

        // $now = Carbon::now()->format('Y-m-d H:i:s');

        return new Account($formatted);
    }
}
