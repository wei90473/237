<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T09tb extends Model
{
    protected $table = 't09tb';

    public $timestamps = false;

    protected $fillable = array('class', 'term', 'course', 'idno', 'type', 'kind', 'insuremk1', 'insuremk2', 'lecthr', 'lectamt', 'noteamt', 'speakamt', 'teachtot', 'motoramt', 'trainstart', 'trainamt', 'planestart', 'planeamt', 'otheramt', 'tratot', 'deductrate', 'deductamt1', 'deductamt2', 'deductamt', 'insureamt1', 'insureamt2', 'insuretot', 'netpay', 'totalpay', 'handout', 'insureday', 'paidday', 'taxedday', 'okrate', 'paymk', 'motoramt_o', 'motoramt_d', 'ship_o', 'ship_d', 'ship', 'train_o', 'train_d', 'mrt_o', 'mrt_d', 'mrtamt', 'plane_d', 'no_tax', 'review_unit_price', 'review_quantity', 'review_total', 'other_salary');
}