<?php
namespace App\Repositories;

use App\Models\StudentGrade;
use App\Repositories\Repository;
use DateTime;

class StudentGradeRepository extends Repository
{
    public function __construct(StudentGrade $student_grade)
    {
        $this->model = $student_grade;
    }  
    
}