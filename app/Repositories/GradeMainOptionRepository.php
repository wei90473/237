<?php
namespace App\Repositories;

use App\Models\Grade_main_option;
use App\Repositories\Repository;
use DB;

class GradeMainOptionRepository extends Repository
{   
    public function __construct(Grade_main_option $grade_main_option)
    {
        $this->model = $grade_main_option;
    }  

    public function getGrades($t04tb_info)
    {
        $sql = "SELECT idno, 
                       sum(real_grade * grade_main_option.persent / 100) final_grade, 
                       rank() over(order by sum(real_grade * grade_main_option.persent / 100) desc) rank
                FROM 
                    (
                        SELECT 
                            student_grades.idno,
                            grade_main_option.id main_option_id,
                            sum(student_grades.grade * grade_sub_option.persent / 100) real_grade
                        FROM grade_main_option 
                        JOIN grade_sub_option ON grade_sub_option.main_option_id = grade_main_option.id
                        JOIN student_grades ON student_grades.sub_option_id = grade_sub_option.id
                        WHERE (`grade_main_option`.`class` = ? and `grade_main_option`.`term` = ?)
                        GROUP BY student_grades.idno, grade_main_option.id
                    ) a
                JOIN grade_main_option on grade_main_option.id = main_option_id
                GROUP BY idno";

        $total_grades = DB::select($sql, [$t04tb_info['class'], $t04tb_info['term']]);   

        $sql = "SELECT 
                    student_grades.idno,
                    grade_main_option.id main_option_id,
                    sum(student_grades.grade * grade_sub_option.persent / 100) real_grade
                FROM grade_main_option 
                JOIN grade_sub_option ON grade_sub_option.main_option_id = grade_main_option.id
                JOIN student_grades ON student_grades.sub_option_id = grade_sub_option.id
                WHERE (`grade_main_option`.`class` = ? and `grade_main_option`.`term` = ?)
                GROUP BY student_grades.idno, grade_main_option.id";
        
        $main_grades = DB::select($sql, [$t04tb_info['class'], $t04tb_info['term']]);                 

        return compact(['total_grades', 'main_grades']);

    }

}
