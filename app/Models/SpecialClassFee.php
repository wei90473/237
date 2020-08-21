<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialClassFee extends Model
{
    protected $table = 'special_class_fee';

    protected $guarded = array('id');

    public function t04tb()
    {
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);        
    }
    /*
        Attribute 專區
    */
    // public function getServiceFeeQuantityAttribute()
    // {
    //     $quantitys = ['oh_hourly_fee_quantity', 'ohbe_hourly_fee_quantity', 'ih_hourly_fee_quantity', 'ass_hourly_fee_quantity', 'business_pay_quantity', 'food_expenses_quantity', 'rent_car_quantity', 'insurance_quantity', 'reward_quantity'];

    //     $service_fee_quantity = 0;
    //     foreach ($quantitys as $quantity){
    //         if (isset($this->$quantity)){
    //             $service_fee_quantity += $this->$quantity;
    //         }
    //     }
    //     return $service_fee_quantity;
    // }

    // public function getServiceFeeUnitPriceAttribute()
    // {
    //     $unit_prices = ['oh_hourly_fee_unit_price', 'ohbe_hourly_fee_unit_price', 'ih_hourly_fee_unit_price', 'ass_hourly_fee_unit_price', 'business_pay_unit_price', 'food_expenses_unit_price', 'rent_car_unit_price', 'insurance_unit_price', 'reward_unit_price'];

    //     $service_fee_unit_price = 0;
    //     foreach ($unit_prices as $unit_price){
    //         if (isset($this->$unit_price)){
    //             $service_fee_unit_price += $this->$unit_price;
    //         }
    //     }
    //     return $service_fee_unit_price;
    // }
    
    // public function getServiceFeeDaysAttribute()
    // {
    //     $days = ['food_expenses_days'];

    //     $service_fee_days = 0;
    //     foreach ($days as $day){
    //         if (isset($this->$day)){
    //             $service_fee_days += $this->$day;
    //         }
    //     }
    //     return $service_fee_days;
    // } 
    
    // public function getServiceFeeBudgetAttribute()
    // {
    //     $budgets = ['oh_hourly_fee_budget', 'ohbe_hourly_fee_budget', 'ih_hourly_fee_budget', 'ass_hourly_fee_budget', 'business_pay_budget', 'food_expenses_budget', 'rent_car_budget', 'insurance_budget', 'reward_budget'];

    //     $service_fee_budget = 0;
    //     foreach ($budgets as $budget){
    //         if (isset($this->$budget)){
    //             $service_fee_budget += $this->$budget;
    //         }
    //     }
    //     return $service_fee_budget;
    // }    

    // public function getHourlyFeeUnitPriceAttribute()
    // {
    //     $unit_prices = ['oh_hourly_fee_unit_price', 'ohbe_hourly_fee_unit_price', 'ih_hourly_fee_unit_price', 'ass_hourly_fee_unit_price'];

    //     $hourly_fee_unit_price = 0;
    //     foreach ($unit_prices as $unit_price){
    //         if (isset($this->$unit_price)){
    //             $hourly_fee_unit_price += $this->$unit_price;
    //         }
    //     }
    //     return $hourly_fee_unit_price;
    // }    

    // public function getHourlyFeeBudgetAttribute()
    // {
    //     $budgets = ['oh_hourly_fee_budget', 'ohbe_hourly_fee_budget', 'ih_hourly_fee_budget', 'ass_hourly_fee_budget'];

    //     $hourly_fee_budget = 0;
    //     foreach ($budgets as $budget){
    //         if (isset($this->$budget)){
    //             $hourly_fee_budget += $this->$budget;
    //         }
    //     }
    //     return $hourly_fee_budget;
    // }        

    // public function getExtraQuantityAttribute()
    // {
    //     $quantitys = ['rent_car_quantity', 'insurance_quantity', 'reward_quantity'];

    //     $extra_quantity = 0;
    //     foreach ($quantitys as $quantity){
    //         if (isset($this->$quantity)){
    //             $extra_quantity += $this->$quantity;
    //         }
    //     }
    //     return $extra_quantity;
    // }    

    // public function getExtraBudgetAttribute()
    // {
    //     $budgets = ['rent_car_budget', 'insurance_budget', 'reward_budget'];

    //     $extra_budget = 0;
    //     foreach ($budgets as $budget){
    //         if (isset($this->$budget)){
    //             $extra_budget += $this->$budget;
    //         }
    //     }
    //     return $extra_budget;
    // }    

}