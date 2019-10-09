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


use iBrand\Coterie\Core\Models\Question;
use iBrand\Component\User\Models\User;


class QuestionPolicy
{

    public function isQuestionAnswerUser(User $user, Question $question)
    {
        return  $user->id === $question->answer_user_id;
    }

}
