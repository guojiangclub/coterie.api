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

use iBrand\Coterie\Core\Models\Coterie;
use iBrand\Component\User\Models\User;


class CoteriePolicy
{

    public function isCoterieOwner(User $user, Coterie $coterie)
    {
        return  $user->id === $coterie->user_id;
    }


}
