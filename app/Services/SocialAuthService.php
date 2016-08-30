<?php

namespace Kabooodle\Services;

use Kabooodle\Models\User;
use Kabooodle\Models\SocialAccount;
use Laravel\Socialite\Contracts\User as ProviderUser;

/**
 * Class SocialAccountService
 * @package Kabooodle\Services
 */
class SocialAccountService
{
    /**
     * @param ProviderUser $providerUser
     *
     * @return User
     */
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialAccount::whereProvider('facebook')
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'facebook'
            ]);

            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {

                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }
}