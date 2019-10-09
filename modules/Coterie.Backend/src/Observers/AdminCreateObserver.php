<?php
namespace iBrand\Coterie\Backend\Observers;

use iBrand\Backend\Models\Admin;
use iBrand\Component\Account\Models\Account;
use iBrand\Component\Account\Models\AccountApplication;
use Illuminate\Support\Facades\Cookie;

class AdminCreateObserver
{
    public function saved(Admin $admin)
    {
        if (!$account = Account::where('mobile', $admin->mobile)->first()) {
            $account = Account::create([
                'mobile' => $admin->mobile,
                'password' => $admin->password
            ]);
        }

        $uuid = Cookie::get('ibrand_log_uuid');

        if ($uuid AND
            $application = AccountApplication::where('uuid', $uuid)->first() AND
            !$account->applications->where('id', $application->id)->first()
        ) {

            $account->applications()->attach($application->id, ['type' => 'staff']);
        }

    }
}