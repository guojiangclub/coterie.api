<?php

/*
 * This file is part of ibrand/coterie.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iBrand\Coterie\Core\Policies;

use iBrand\Coterie\Core\Models\Praise;
use iBrand\Component\User\Models\User;

class PraisePolicy
{

    public function isPraiseUser(User $user, Praise $praise)
    {
        return  $user->id === $praise->user_id;
    }


}
