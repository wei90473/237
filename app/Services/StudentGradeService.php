<?php
namespace App\Services;
use App\Repositories\T04tbRepository;
use App\Repositories\GradeMainOptionRepository;
use App\Repositories\GradeSubOptionRepository;
use App\Repositories\StudentGradeRepository;


use App\Helpers\Des;
use DateTime;
use DB;

class StudentGradeService
{
    /**
     * StudentGradeService constructor.
     * @param T04tbRepository $t04tbRepository
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        GradeMainOptionRepository $gradeMainOptionRepository,
        GradeSubOptionRepository $gradeSubOptionRepository,
        StudentGradeRepository $studentGradeRepository     
    )
    {
        $this->t04tbRepository = $t04tbRepository; 
        $this->gradeMainOptionRepository = $gradeMainOptionRepository; 
        $this->gradeSubOptionRepository = $gradeSubOptionRepository;   
        $this->studentGradeRepository = $studentGradeRepository;
    }

    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($t04tb_info, $with = false)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }

    public function computeGrades($t04tb_info)
    {
        $grades = $this->gradeMainOptionRepository->getGrades($t04tb_info);

        $grades['total_grades'] = collect($grades['total_grades'])->keyBy('idno');

        $main_grades = [];

        foreach($grades['main_grades'] as $main_grade){
            $main_grades[$main_grade->idno][$main_grade->main_option_id] = $main_grade;
        }

        $grades['main_grades'] = $main_grades;

        return $grades;
    }

    public function storeGradeMainOption($t04tb_info, $main_options, $action)
    {
        if ($action == "insert"){
            foreach($main_options as $main_option){
                if (count(array_filter($main_option)) <> 2 && $main_option['persent'] === ""){
                    continue;
                } 
                $main_option['class'] = $t04tb_info['class'];
                $main_option['term'] = $t04tb_info['term'];
                $this->gradeMainOptionRepository->insert($main_option);
            }
        }else if ($action == "update"){
            foreach($main_options as $id => $main_option){
                $this->gradeMainOptionRepository->update(['id' => $id], $main_option);
            }
        }
    }

    public function check100Persent($options)
    {
        $persent = 0;

        $total_option_num = 0;

        if (!empty($options['new_main_option'])){
            foreach ($options['new_main_option'] as $new_main_option){
                $persent += (int)$new_main_option['persent'];
            }            
        }

        if (!empty($options['main_option'])){
            foreach ($options['main_option'] as $main_option){
                $persent += (int)$main_option['persent'];
            }            
        }

        return ($persent == 100);

    }

    public function checkSubOption100Persent($options)
    {
        $persent = 0;

        $total_option_num = 0;

        if (!empty($options['new_sub_option'])){
            $total_option_num += count($options['new_sub_option']);
            foreach ($options['new_sub_option'] as $new_sub_option){
                $persent += (int)$new_sub_option['persent'];
            }            
        }

        if (!empty($options['sub_option'])){
            $total_option_num += count($options['sub_option']);
            foreach ($options['sub_option'] as $sub_option){
                $persent += (int)$sub_option['persent'];
            }            
        }

        if ($total_option_num > 5) return false;

        return ($persent == 100);

    }

    public function getMainOption($id)
    {
        return $this->gradeMainOptionRepository->find($id);
    }

    public function storeGradeSubOption($main_option_id, $sub_options, $action)
    {
        if ($action == "insert"){
            foreach($sub_options as $sub_option){
                if (count(array_filter($sub_option)) <> 2 && $sub_option['persent'] === ""){
                    continue;
                } 
                $sub_option['main_option_id'] = $main_option_id;
                $this->gradeSubOptionRepository->insert($sub_option);
            }
        }else if ($action == "update"){
            foreach($sub_options as $id => $sub_option){
                $this->gradeSubOptionRepository->update(['id' => $id], $sub_option);
            }
        }
    }

    public function storeGrade($sub_options)
    {
        // dd($sub_options);
        DB::beginTransaction();
        try {

            foreach($sub_options as $sub_option_id => $grades){
                foreach($grades as $idno => $grade){
                    if (!empty($grade) && $grade !== "" && $grade !== null){
                        $this->studentGradeRepository->updateOrCreate(['sub_option_id' => $sub_option_id, 'idno' => $idno], ['grade' => $grade]);
                    }else{
                        $this->studentGradeRepository->update(['sub_option_id' => $sub_option_id, 'idno' => $idno], ['grade' => $grade]);
                    }
                }
            }
            DB::commit();
            
            return true; 

        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;            
            return false; 

            // return back()->with('result', 0)->with('message', '更新失敗');

        } 


    }

    public function deleteSubOptions($sub_option_ids)
    {
        // 刪除成績
        $this->gradeSubOptionRepository->deleteByKeys('id', $sub_option_ids);
        // 刪除子項目
        $this->studentGradeRepository->deleteByKeys('sub_option_id', $sub_option_ids);
    }

    public function deleteMainOptions($main_option_ids)
    {
        $sub_option_ids = $this->gradeSubOptionRepository->getByKeys('main_option_id', $main_option_ids)->pluck('id');

        $this->deleteSubOptions($sub_option_ids);

        // 刪除主項目
        $this->gradeMainOptionRepository->deleteByKeys('id', $main_option_ids);
    }

    public function getMainOptionsByName($names, $t04tb_info)
    {
        return $this->gradeMainOptionRepository->getByKeys('name', $names, $t04tb_info);
    }

    public function transMainOptionImportData($main_options, $t04tb_info)
    {
        $main_options = collect($main_options)->map(function ($main_option, $key){
            $key = trim($key);
            return [
                'name' => trim($main_option[0]),
                'persent' => $main_option[1]
            ];
        })->keyBy('name');

        $main_option_names = collect($main_options)->map(function ($main_option){
            return $main_option['name'];
        });

        $origin_grade_main_options = $this->getMainOptionsByName($main_option_names, $t04tb_info)->keyBy('id');

        $req_sub_option = $origin_grade_main_options->map(function($grade_main_option) use($main_options){
            return $main_options[$grade_main_option->name];
        })->toArray();

        $new_main_option = array_diff($main_options->pluck('name')->toArray(), $origin_grade_main_options->pluck('name')->toArray());

        $req_new_sub_option = collect($new_main_option)->map(function($new_main_option_name) use($main_options){
            return $main_options[$new_main_option_name]; 
        })->toArray();

        return [
            'main_option' => $req_sub_option,
            'new_main_option' => $req_new_sub_option
        ];

    }

    public function transSubOptionImportData($sub_options, $t04tb_info)
    {
        $sub_options = collect($sub_options)->map(function ($sub_option, $key){
            $key = trim($key);
            return [
                'name' => trim($sub_option[0]),
                'persent' => $sub_option[1]
            ];
        })->keyBy('name');

        $sub_option_names = collect($sub_options)->map(function ($sub_option){
            return $sub_option['name'];
        });

        $origin_grade_sub_options = $this->getSubOptionsByName($sub_option_names, $t04tb_info)->keyBy('id');

        $req_sub_option = $origin_grade_sub_options->map(function($grade_sub_option) use($sub_options){
            return $sub_options[$grade_sub_option->name];
        })->toArray();

        $new_sub_option = array_diff($sub_options->pluck('name')->toArray(), $origin_grade_sub_options->pluck('name')->toArray());

        $req_new_sub_option = collect($new_sub_option)->map(function($new_sub_option_name) use($sub_options){
            return $sub_options[$new_sub_option_name]; 
        })->toArray();

        return [
            'sub_option' => $req_sub_option,
            'new_sub_option' => $req_new_sub_option
        ];

    }

    public function getSubOptionsByName($names, $t04tb_info)
    {
        return $this->gradeSubOptionRepository->getByKeys('name', $names, $t04tb_info);
    }
}
