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

use iBrand\Coterie\Core\Models\Comment;
use iBrand\Component\User\Models\User;


class CommentPolicy
{

    public function isCommentUser(User $user, Comment $comment)
    {
        return  $user->id === $comment->user_id;
    }


}
