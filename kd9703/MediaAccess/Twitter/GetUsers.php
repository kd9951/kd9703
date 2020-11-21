<?php
namespace Kd9703\MediaAccess\Twitter;

use Carbon\Carbon;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\MediaAccess\Interfaces\GetUsers as GetUsersInterface;
use Kd9703\MediaAccess\Twitter\Tools\FormatUserObjectForAccount;

/**
 * 通知を取得
 */
class GetUsers extends MediaAccess implements GetUsersInterface
{
    use FormatUserObjectForAccount;

    const ENDPOINT_USER = '/users/lookup'; // :account_id

    /**
     * @param  Account $account
     * @param  Account $target_account
     * @return mixed
     */
    public function exec(Account $account, Accounts $target_accounts): Accounts
    {
        if ($target_accounts->count() == 0) {
            $this->system_logger->notice('No target given.');
            return $target_accounts;
        }

        $this->wait->waitNormal('twitter.GetUsers', 0, 0);

        $url   = self::ENDPOINT_USER;
        $param = ['user_id' => implode(',', $target_accounts->pluck('account_id'))];
        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $response_json_array = $this->client->getContentAs('json.array');

        if (!is_array($response_json_array)) {
            $this->system_logger->error('/users/lookup returns invalid json.', compact('url', 'param', 'response_json_array'));
            $formatted = [];
        } else {
            $formatted = $this->format($response_json_array);
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');
        foreach ($target_accounts as $target_account) {
            if (isset($formatted[$target_account->account_id])) {
                foreach ($formatted[$target_account->account_id] as $key => $value) {
                    if (!is_null($value)) {
                        $target_account->$key = $value;
                    }
                }
                $target_account->reviewed_at = $now;
            }
        }

        return $target_accounts;
    }

    /**
     * パターン抽出
     *
     * @return mixed
     */
    protected function format($response_json_array)
    {
        $formatted = [];

        foreach ($response_json_array as $user) {

            $account = $this->FormatUserObjectForAccount($user);

            $account_id = $account['account_id'];
            unset($account['account_id']);

            $formatted[$account_id] = $account;
        }

        return $formatted;
    }

}
