<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/3
 * Time: 10:18
 */

namespace iBrand\Coterie\Backend\Models;


use iBrand\Component\User\Models\User;

class Question extends \iBrand\Coterie\Core\Models\Question
{
    public function coterie()
    {
        return $this->belongsTo(Coterie::class, 'coterie_id');
    }

    public function atUser()
    {
        return $this->belongsTo(User::class, 'answer_user_id');
    }

    public function content()
    {
        return $this->belongsTo(Content::class, 'answer_user_id');
    }
}