<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainQuestSetting extends Model
{
    protected $table = 'train_questionnaires_settings';
    protected $casts = ['class' => 'string'];
    protected $fillable = array('class' ,'term' ,'url', 'type', 'created_at', 'updated_at');

    public function getTypeNameAttribute(){
        $type = config('app.train_quest_type');
        $type_name = (empty($type[$this->type])) ? "" : $type[$this->type];
        return $type_name;
    }

    public function t04tb(){
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }

    public function trainQuestionQuestionnaires()
    {
        return $this->hasMany('App\Models\TrainQuestionnaire', 'setting_id', 'id')->where('type', '=', 1);;
    }

    public function trainAnswerQuestionnaires()
    {
        return $this->hasMany('App\Models\TrainQuestionnaire', 'setting_id', 'id')->where('type', '=', 2);
    }    




}