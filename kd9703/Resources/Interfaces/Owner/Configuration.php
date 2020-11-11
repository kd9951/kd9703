<?php
namespace Kd9703\Resources\Interfaces\Owner;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Owner\Configration as ConfigrationsEntity;

/**
 * 設定
 */
interface Configuration
{
    /**
     * @param Account $account
     */
    public function create(Account $account): ConfigrationsEntity;

    /**
     * @param Account $account
     */
    public function get(Account $account): ConfigrationsEntity;

    /**
     * @param Account             $account
     * @param ConfigrationsEntity $configration
     */
    public function store(Account $account, ConfigrationsEntity $configration): ConfigrationsEntity;
}
