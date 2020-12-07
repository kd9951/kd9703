<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Owner\Configration as ConfigrationEntity;
use Kd9703\Resources\Interfaces\Owner\Configuration;

class ConfigurationController extends BaseController
{
    /**
     * @param Configuration $ConfigurationResource
     */
    public function show(Configuration $ConfigurationResource)
    {
        $account = Auth::check() ? Auth::user()->getAccount() : null;

        if (!$account) {
            abort(401, 'Account not found.');
        }

        $configuration = $ConfigurationResource->get($account);

        session(['config_previous' => url()->previous()]);

        return view('configuration', [
            'account'       => $account,
            'configuration' => $configuration,
        ]);
    }

    /**
     * @param Configuration $ConfigurationResource
     */
    public function update(Request $request, Configuration $ConfigurationResource)
    {
        $account = Auth::user()->getAccount();

        if (!$account) {
            abort(401, 'Account not found.');
        }

        $configuration = new ConfigrationEntity([]);

        $input = $request->all();

        if (isset($input['follow_only_tweets_more_than'])) {
            $input['follow_only_tweets_more_than'] = $input['follow_only_tweets_more_than'] == 'on' ? 1 : 0;
        }
        if (isset($input['follow_back_only_tweets_more_than'])) {
            $input['follow_back_only_tweets_more_than'] = $input['follow_back_only_tweets_more_than'] == 'on' ? 1 : 0;
        }

        foreach ($configuration->getKeys() as $key) {
            if (isset($input[$key])) {
                $configuration->$key = $input[$key];
            }
        }

        $configuration = $ConfigurationResource->store($account, $configuration);

        Auth::user()->configRefresh();

        return session()->has('links') ? redirect()->route('dashboard') : redirect()->to(session('config_previous'));
    }
}
