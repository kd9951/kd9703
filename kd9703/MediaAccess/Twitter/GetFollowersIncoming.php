<?php
namespace Kd9703\MediaAccess\Twitter;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\MediaAccess\Interfaces\GetFollowersIncoming as GetFollowersIncomingInterface;
use Kd9703\MediaAccess\Twitter\Tools\FormatUserObjectForAccount;

/**
 * 通知を取得
 */
class GetFollowersIncoming extends MediaAccess implements GetFollowersIncomingInterface
{
    use FormatUserObjectForAccount;

    const ENDPOINT_INCOMING = '/friendships/incoming'; // :account_id

    /**
     * @param  Account $account
     * @param  Account $target_account
     * @return array   $account_ids
     */
    public function exec(Account $account): array
    {
        $this->wait->waitNormal('twitter.GetFollowersIncoming', 0, 0);

        $url   = self::ENDPOINT_INCOMING;
        $param = [
            // stringify_ids
            // cursor
        ];
        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $response_json_array = $this->client->getContentAs('json.array');

        if (!is_array($response_json_array)) {
            $this->system_logger->error(self::ENDPOINT_INCOMING . ' returns invalid json.', compact('url', 'param', 'response_json_array'));
            $account_ids = [];
        } else {
            $account_ids = $response_json_array['ids'] ?? [];
        }

        return $account_ids;
    }
}
