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

use iBrand\Coterie\Core\Models\Order;
use iBrand\Component\User\Models\User;

class OrderPolicy
{

    public function isOrderUser(User $user, Order $order)
    {
        return  $user->id === $order->user_id;
    }

    public function isPaymentOrderUser(User $user, Order $order)
    {
        return  ($user->id === $order->user_id AND empty($order->paid_at));
    }
}
