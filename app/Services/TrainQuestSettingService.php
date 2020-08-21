<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\TrainQuestSettingRepository;
use App\Repositories\TrainQuestionnaireRepository;

class TrainQuestSettingService
{
    public function __construct(
        T04tbRepository $t04tbRepository,
        TrainQuestSettingRepository $trainQuestSettingRepository,
        TrainQuestionnaireRepository $trainQuestionnaireRepository
        )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->trainQuestSettingRepository = $trainQuestSettingRepository;
        $this->trainQuestionnaireRepository = $trainQuestionnaireRepository;
    }

    function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    function getClass($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }

    function getTrainQuestSettings($class, $term)
    {
        return $this->trainQuestSettingRepository->get($class, $term);
    }

    function getTrainQuestSetting($id)
    {
        return $this->trainQuestSettingRepository->find($id);
    }

    function createtrainQuestSetting($train_quest_setting)
    {
        return $this->trainQuestSettingRepository->create($train_quest_setting);
    }

    function uploadQuestionnaire($quest_setting, $question_file = null, $answer_file = null)
    {
        $rand_str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $insert = [];
        // 上傳問卷題目檔
        // dd($question_file->isValid());
        if ($question_file !== null){
            if ($question_file->isValid()){
                $extension = $question_file->getClientOriginalExtension(); //副檔名
                $origin_name = $question_file->getClientOriginalName();
                $file_name = $quest_setting->class.'_'.$quest_setting->term.'_'.substr(str_shuffle($rand_str), 26, 5).'_'.time().".".$extension;    //重新命名
                $upload_path = '/Uploads/train_questionnaire/question/';
                $question_file->move(public_path().$upload_path, $file_name);
                if (file_exists(public_path().$upload_path.$file_name)){
                    $train_questionnaire = [
                        "setting_id" => $quest_setting->id,
                        "type" => 1,
                        "path" => $file_name,
                        "origin_name" => $origin_name
                    ];
                    $insert[] = $this->trainQuestionnaireRepository->create($train_questionnaire);
                }
            }
        }

        // 上傳問卷答案檔
        if ($answer_file !== null){
            if ($answer_file->isValid()){
                $extension = $answer_file->getClientOriginalExtension(); //副檔名
                $origin_name = $answer_file->getClientOriginalName();
                $file_name = $quest_setting->class.'_'.$quest_setting->term.'_'.substr(str_shuffle($rand_str), 26, 5).'_'.time().".".$extension;    //重新命名
                $upload_path = '/Uploads/train_questionnaire/answer/';
                $answer_file->move(public_path().$upload_path, $file_name);
                if (file_exists(public_path().$upload_path.$file_name)){
                    $train_questionnaire = [
                        "setting_id" => $quest_setting->id,
                        "type" => 2,
                        "path" => $file_name,
                        "origin_name" => $origin_name
                    ];
                    $insert[] = $this->trainQuestionnaireRepository->create($train_questionnaire);
                }
            }
        }
        return $insert;
    }
    public function deleteTrainQuestionnaire($id){
        $train_questionnaire = $this->trainQuestionnaireRepository->find($id);
        if(isset($train_questionnaire))
        return $train_questionnaire->delete();
    }

    public function deleteTrainQuestSetting($id){
        $train_quest_setting = $this->trainQuestSettingRepository->find($id);
        if (!empty($train_quest_setting)){
            $question_questionaires = $train_quest_setting->trainQuestionQuestionnaires;
            $answer_questionaires = $train_quest_setting->trainAnswerQuestionnaires;

            if (!empty($question_questionaires)){
                foreach ($question_questionaires as $questionaire){
                    $questionaire->delete();
                }
            }

            if (!empty($answer_questionaires)){
                foreach ($answer_questionaires as $questionaire){
                    $questionaire->delete();
                }
            }

            $train_quest_setting->delete();
            return $train_quest_setting;
        }else{
            return false;
        }

    }
}