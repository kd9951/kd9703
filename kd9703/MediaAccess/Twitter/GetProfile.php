<?php
namespace Kd9703\MediaAccess\Twitter;

use Crawler\HttpClientInterface;
use Crawler\ParserInterface;
use Crawler\Support\Random;
use Crawler\Support\Wait;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\MediaAccess\Interfaces\GetProfile as GetProfileInterface;

/**
 * 通知を取得
 */
class GetProfile extends MediaAccess implements GetProfileInterface
{
    /**
     * @var GetUsers
     */
    protected $GetUsers;

    /**
     * Undocumented function
     *
     * @param HttpClientInterface $client
     * @param ParserInterface     $parser
     */
    public function __construct(
        GetUsers $GetUsers,
        HttpClientInterface $client,
        ParserInterface $parser,
        SystemLogger $system_logger,
        OwnerLogger $owner_logger,
        Wait $wait,
        Random $random
    ) {
        $this->GetUsers = $GetUsers;

        parent::__construct(
            $client,
            $parser,
            $system_logger,
            $owner_logger,
            $wait,
            $random
        );
    }

    /**
     * @param  Account $account
     * @param  Account $target_account
     * @return mixed
     */
    public function exec(Account $account, Account $target_account): Account
    {
        $target_accounts = new Accounts([$target_account]);

        $target_accounts = ($this->GetUsers)([
            'account'         => $account,
            'target_accounts' => $target_accounts,
        ]);

        return $target_accounts[0];
    }

}
