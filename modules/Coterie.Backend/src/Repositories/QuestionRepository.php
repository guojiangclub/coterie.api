<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/1/3
 * Time: 10:18
 */

namespace iBrand\Coterie\Backend\Repositories;


use iBrand\Coterie\Backend\Models\Question;
use Prettus\Repository\Eloquent\BaseRepository;

class QuestionRepository extends BaseRepository
{
    public function model()
    {
        return Question::class;
    }

    public function getQuestionByID($id)
    {
        return $this->scopeQuery(function ($query) {
            return $query->withTrashed();
        })->find($id);
    }

    public function getQuestionPaginate($where, $limit = 15)
    {
        return $this->scopeQuery(function ($query) use ($where) {
            if (key_exists('forbidden', $where)) {
                unset($where['forbidden']);
                $query = $query->onlyTrashed();
            }

            if (count($where) AND is_array($where)) {
                foreach ($where as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
            return $query->orderBy('created_at', 'desc');
        })->paginate($limit);
    }
}