<?php
namespace App\Repositories;

use App\Models\TrainQuestSetting;

class TrainQuestSettingRepository
{
    public function __construct(TrainQuestSetting $trainQuestSetting)
    {
        $this->trainQuestSetting = $trainQuestSetting;
    }    

    public function get($class, $term)
    {
        $paginate_qty = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 10;
        return $this->trainQuestSetting->where("class", "=", $class)
                                       ->where("term", "=", $term)
                                       ->paginate($paginate_qty);
    }

    public function create($train_quest_setting)
    {
        return $this->trainQuestSetting->create($train_quest_setting);
    }

    public function find($id){
        return $this->trainQuestSetting->with([])->find($id);
    }
}