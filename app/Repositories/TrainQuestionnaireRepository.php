<?php
namespace App\Repositories;

use App\Models\TrainQuestionnaire;

class TrainQuestionnaireRepository
{
    public function __construct(TrainQuestionnaire $trainQuestionnaire)
    {
        $this->trainQuestionnaire = $trainQuestionnaire;
    }    

    public function get()
    {

    }

    public function find($id)
    {
        return $this->trainQuestionnaire->find($id);
    }

    public function create($train_questionnaire)
    {
        return $this->trainQuestionnaire->create($train_questionnaire);
    }
}