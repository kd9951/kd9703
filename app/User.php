<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kd9703\Entities\Media\Account;

/**
 * Laravelフレームワークが使用するための「認証されたログインユーザー」
 * Ownerエンティティに依存（搭載）
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var mixed
     */
    protected $account = null;

    /**
     * @param Owner $account
     */
    public function __construct(?Account $account = null)
    {
        if ($account) {
            $this->setAccount($account);
        }
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @return mixed
     */
    public function isAdmin(): bool
    {
        return $this->account->account_id == config('services.twitter.owner_twitter_id');
    }

    /**
     * @var string
     */
    protected $table = 'has no tables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthIdentifier()
    {
        return $this->account->account_id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->account->password;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->account->username;
    }

}
