<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\SpecialClassFeeRepository;

class SpecialClassFeeService
{
    /**
     * StudentService constructor.
     * @param M02tbRepository $m02tbRepository
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        SpecialClassFeeRepository $specialClassFeeRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->specialClassFeeRepository = $specialClassFeeRepository;
    }

    public function getT04tbs($queryData)
    {
        $queryData['t01tb']['process'] = 2;
        $queryData['special_class_fee'] = true;
        $t04tbs = $this->t04tbRepository->getByQueryList($queryData, ['t04tb.*', 'special_class_fee.id']);
        return $t04tbs;
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }

    public function storeSpecialClassFee($t04tb, $special_class_fee)
    {
        $t04tb->realQuota = $t04tb->t13tbs->count();
        // $t04tb->realQuota = 50;
        /*
            當 40 人以下 (不含 40 人)

            B = B的四項子項目加起來的總和
            B1 單價 2000
            B2 單價 1500
            B3 單價 1000
            B4 單價 500
            C = 住宿班一天 14900，不住宿班一天 5900，預算數等於上面的金額*天數
            D = 一人一天175，預算數 等於 金額 * 人 * 天 
            E = E的三項子項目加起來的總和，E項都沒有預設值，自由填
            最後 A 等於 B + C + D + E     
            
            當40人(含)以上算法：

            A = 人數 * 天數 * 單價(住宿班 $820、不住宿班 $600) + E額外項
            B 的算法一樣
            C = A - B  - D - E 
            D 的算法一樣
            E 不自動算，自由填
        */
        $fee = $t04tb->specailClassFee->fill($special_class_fee)->toArray();

        // C-業務支出
        if ($t04tb->realQuota < 40){
            if ($fee['business_pay_unit_price'] == null){
                // C 業務支出單價 如不提供住宿 單價 5900 提供住宿 14900
                $fee['business_pay_quantity'] = $t04tb->t01tb->day;
                $fee['business_pay_unit_price'] = ($t04tb->t01tb->board == 'X') ? 5900 : 14900;  

                $fee['service_fee_quantity'] = null;
                $fee['service_fee_unit_price'] = null;
                $fee['service_fee_days'] = null;
            }

        }else{

            if ($fee['business_pay_unit_price'] !== null){
                // 大於(包含) 40 人時計算方式不同所以數量為 1 無單價
                $fee['business_pay_quantity'] = 1;
                $fee['business_pay_unit_price'] = null;

                // 業務費
                $fee['service_fee_quantity'] = $t04tb->realQuota;
                $fee['service_fee_unit_price'] = ($t04tb->t01tb->board == 'X') ? 600 : 820;
                $fee['service_fee_days'] = $t04tb->t01tb->day;
            }
            
        }
        
        $computedFee = $this->computeBudget($t04tb, $fee);
        
        $newFee = $t04tb->specailClassFee->fill($computedFee)->toArray();
        $feeKey = collect($t04tb->toArray())->only(['class', 'term'])->toArray();

        $this->specialClassFeeRepository->update($feeKey, $newFee);
        return true;
    }

    // public function computeFoodFee($t04tb, $special_class_fee)
    // {
    //     $food_expenses_unit_price = 175;
    //     $food_expenses_days = $t04tb->t01tb->day;

    //     $food_expenses_quantity = (isset($special_class_fee['food_expenses_quantity']) && $special_class_fee['food_expenses_quantity'] != "") ? $special_class_fee['food_expenses_quantity'] : $t04tb->t01tb->quota; 
    //     $food_expenses_budget = (int)$special_class_fee['food_expenses_quantity'] * (int)$food_expenses_unit_price * (int)$food_expenses_days;
    //     return compact(['food_expenses_quantity', 'food_expenses_unit_price', 'food_expenses_days', 'food_expenses_budget']);
    // }

    // public function computeSubHourlyFee($special_class_fee)
    // {
    //     $oh_hourly_fee_unit_price = 2000;
    //     $ohbe_hourly_fee_unit_price = 1500;
    //     $ih_hourly_fee_unit_price = 1000;
    //     $ass_hourly_fee_unit_price = 500;

    //     $oh_hourly_fee_budget = (int)$special_class_fee['oh_hourly_fee_quantity'] * (int)$oh_hourly_fee_unit_price;
    //     $ohbe_hourly_fee_budget = (int)$special_class_fee['ohbe_hourly_fee_quantity'] * (int)$ohbe_hourly_fee_unit_price;
    //     $ih_hourly_fee_budget = (int)$special_class_fee['ih_hourly_fee_quantity'] * (int)$ih_hourly_fee_unit_price;
    //     $ass_hourly_fee_budget = (int)$special_class_fee['ass_hourly_fee_quantity'] * (int)$ass_hourly_fee_unit_price;     

    //     return compact(['oh_hourly_fee_unit_price', 'ohbe_hourly_fee_unit_price', 'ih_hourly_fee_unit_price', 'ass_hourly_fee_unit_price', 'oh_hourly_fee_budget', 'ohbe_hourly_fee_budget', 'ih_hourly_fee_budget', 'ass_hourly_fee_budget']);   
    // }

    // public function computeServiceFee($t04tb, $special_class_fee)
    // {
    //     // A-業務費 計算 數量
    //     $service_fee_quantity = (isset($special_class_fee['service_fee_quantity']) && $special_class_fee['service_fee_quantity'] != "") ? $special_class_fee['service_fee_quantity'] : $t04tb->t01tb->quota;

    //     // A-業務費 天數
    //     $service_fee_days = $t04tb->t01tb->day;

    //     if ($t04tb->t01tb->quota < 40){
    //         // A-業務費 計算 預算
    //         $budgets = ['oh_hourly_fee_budget', 'ohbe_hourly_fee_budget', 'ih_hourly_fee_budget', 'ass_hourly_fee_budget', 'business_pay_budget', 'food_expenses_budget', 'rent_car_budget', 'insurance_budget', 'reward_budget'];
    //         $service_fee_budget = 0;
    //         foreach ($budgets as $budget){
    //             if (isset($special_class_fee[$budget])){
    //                 $service_fee_budget += (int)$special_class_fee[$budget];
    //             }
    //         }
    //     }else{
    //         $service_fee_unit_price = ($t04tb->t01tb->board == 'X') ? 600 : 820;
    //         $service_fee_budget = (int)$service_fee_quantity * (int)$service_fee_unit_price * (int)$service_fee_days;
    //     }

    //     return compact(['service_fee_quantity', 'service_fee_unit_price', 'service_fee_days', 'service_fee_budget']);
    // }

    // public function computeHourlyFee($special_class_fee)
    // {

    //     // B-鐘點費 數量
    //     $hourly_fee_quantity = 1;
    //     // B-鐘點費 預算
    //     $budgets = ['oh_hourly_fee_budget', 'ohbe_hourly_fee_budget', 'ih_hourly_fee_budget', 'ass_hourly_fee_budget'];
    //     $hourly_fee_budget = 0;
    //     foreach ($budgets as $budget){
    //         if (isset($special_class_fee[$budget])){
    //             $hourly_fee_budget += (int)$special_class_fee[$budget];
    //         }
    //     }

    //     return compact(['hourly_fee_quantity', 'hourly_fee_budget']);
    // }

    // public function computeExtra($t04tb, $special_class_fee)
    // {
    //     $rent_car_budget = (int)$special_class_fee['rent_car_quantity'] * (int)$special_class_fee['rent_car_unit_price'];
    //     $insurance_budget = (int)$special_class_fee['insurance_quantity'] * (int)$special_class_fee['insurance_unit_price'];
    //     $reward_budget = (int)$special_class_fee['reward_quantity'] * (int)$special_class_fee['reward_unit_price'];

    //     $budgets = ['rent_car_budget', 'insurance_budget', 'reward_budget'];
    //     $extra_budget = 0;
    //     foreach ($budgets as $budget){
    //         if (isset($$budget)){
    //             $extra_budget += (int)$$budget;
    //         }
    //     }

    //     return compact(['extra_budget', 'rent_car_budget', 'insurance_budget', 'reward_budget']);
    // }

    // public function computeBusniessFee($t04tb, $special_class_fee){
    //     if ($t04tb->t01tb->quota < 40){
    //         $business_pay_unit_price = ($t04tb->t01tb->board == 'X') ? 5900 : 14900;
    //         $business_pay_budget = (int)$special_class_fee['business_pay_quantity'] * (int)$business_pay_unit_price * (int)$t04tb->t01tb->day;
    //         return compact(['business_pay_unit_price', 'business_pay_budget']);
    //     }else{
    //         // 數量
    //         $business_pay_quantity = 1;
    //         $business_pay_unit_price = null;

    //         $budgets = ['hourly_fee_budget', 'food_expenses_budget', 'extra_budget'];
    //         $business_pay_budget = $special_class_fee['service_fee_budget'];
    //         foreach ($budgets as $budget){
    //             if (isset($special_class_fee[$budget])){
    //                 $business_pay_budget -= (int)$special_class_fee[$budget];
    //             }
    //         }
  
    //         return compact(['business_pay_quantity', 'business_pay_unit_price', 'business_pay_budget']);        
    //     }  
    // }

    public function computeFeeDefault($t04tb){
        $t04tb->realQuota = $t04tb->t13tbs->count();
        $special_class_fee = [];
        $special_class_fee['class'] = $t04tb->class;
        $special_class_fee['term'] = $t04tb->term;

        // B-鐘點費子項目
        $special_class_fee['oh_hourly_fee_unit_price'] = 2000;      // 外聘鐘點費單價(B-1)
        $special_class_fee['oh_hourly_fee_quantity'] = 0;

        $special_class_fee['ohbe_hourly_fee_unit_price'] = 1500;    // 外聘隸屬鐘點費單價(B-2)
        $special_class_fee['ohbe_hourly_fee_quantity'] = 0;

        $special_class_fee['ih_hourly_fee_unit_price'] = 1000;      // 內聘鐘點費單價(B-3)
        $special_class_fee['ih_hourly_fee_quantity'] = 0;

        $special_class_fee['ass_hourly_fee_unit_price'] = 1500;     // 助教鐘點費單價(B-4)
        $special_class_fee['ass_hourly_fee_quantity'] = 0;
              

        // C-業務支出
        if ($t04tb->realQuota < 40){
            // C 業務支出單價 如不提供住宿 單價 5900 提供住宿 14900
            $special_class_fee['business_pay_quantity'] = $t04tb->realQuota;
            $special_class_fee['business_pay_unit_price'] = ($t04tb->t01tb->board == 'X') ? 5900 : 14900;  
        }else{
            // 大於(包含) 40 人時計算方式不同所以數量為 1 無單價
            $special_class_fee['business_pay_quantity'] = 1;
            $special_class_fee['business_pay_unit_price'] = null;

            // 業務費
            $special_class_fee['service_fee_quantity'] = $t04tb->realQuota;
            $special_class_fee['service_fee_unit_price'] = ($t04tb->t01tb->board == 'X') ? 600 : 820;
            $special_class_fee['service_fee_days'] = $t04tb->t01tb->day;
        }

        // D-伙食費
        $special_class_fee['food_expenses_unit_price'] = 175;
        $special_class_fee['food_expenses_days'] = $t04tb->t01tb->day;
        $special_class_fee['food_expenses_quantity'] = $t04tb->realQuota;

        // E-額外項
        // E-1 租車
        $special_class_fee['rent_car_unit_price'] = 0;
        $special_class_fee['rent_car_quantity'] = 0;
        // E-2 保險
        $special_class_fee['insurance_unit_price'] = 0;        
        $special_class_fee['insurance_quantity'] = 0;
        // E-3 獎品
        $special_class_fee['reward_unit_price'] = 0;
        $special_class_fee['reward_quantity'] = 0;        
        
        $special_class_fee = $this->computeBudget($t04tb, $special_class_fee);
        return $this->specialClassFeeRepository->insert($special_class_fee);

        /*
            當 40 人以下 (不含 40 人)

            B = B的四項子項目加起來的總和
            B1 單價 2000
            B2 單價 1500
            B3 單價 1000
            B4 單價 500
            C = 住宿班一天 14900，不住宿班一天 5900，預算數等於單價*天數
            D = 一人一天175，預算數 等於 金額 * 人 * 天 
            E = E的三項子項目加起來的總和，E項都沒有預設值，自由填
            最後 A 等於 B + C + D + E     
            
            當40人(含)以上算法：

            A = 人數 * 天數 * 單價(住宿班 $820、不住宿班 $600) + E額外項
            B 的算法一樣
            C = A - B  - D - E 
            D 的算法一樣
            E 不自動算，自由填
        */
    }

    public function computeBudget($t04tb, $fee)
    {
        // B-1 外聘鐘點費預算
        $fee['oh_hourly_fee_budget'] = (int)$fee['oh_hourly_fee_quantity'] * (int)$fee['oh_hourly_fee_unit_price'];
        // B-2 外聘隸屬鐘點費預算
        $fee['ohbe_hourly_fee_budget'] = (int)$fee['ohbe_hourly_fee_quantity'] * (int)$fee['ohbe_hourly_fee_unit_price'];
        // B-3 內聘鐘點費預算
        $fee['ih_hourly_fee_budget'] = (int)$fee['ih_hourly_fee_quantity'] * (int)$fee['ih_hourly_fee_unit_price'];
        // B-4 助教鐘點費預算
        $fee['ass_hourly_fee_budget'] = (int)$fee['ass_hourly_fee_quantity'] * (int)$fee['ass_hourly_fee_unit_price'];
        // B 鐘點費
        $fee['hourly_fee_budget'] = (int)$fee['oh_hourly_fee_budget'] + (int)$fee['ohbe_hourly_fee_budget'] + (int)$fee['ih_hourly_fee_budget'] + (int)$fee['ass_hourly_fee_budget'];
        // B 鐘點費數量
        $fee['hourly_fee_quantity'] = (int)$fee['oh_hourly_fee_quantity'] + (int)$fee['ohbe_hourly_fee_quantity'] + (int)$fee['ih_hourly_fee_quantity'] + (int)$fee['ass_hourly_fee_quantity'];


        // D 伙食費預算 = 金額 * 人 * 天;
        $fee['food_expenses_budget'] = (int)$fee['food_expenses_quantity'] * (int)$fee['food_expenses_unit_price'] * (int)$fee['food_expenses_days'];

        // E-1 租車預算
        $fee['rent_car_budget'] = (int)$fee['rent_car_quantity'] * (int)$fee['rent_car_unit_price'];
        // E-2 保險預算
        $fee['insurance_budget'] = (int)$fee['insurance_quantity'] * (int)$fee['insurance_unit_price'];
        // E-3 獎品預算
        $fee['reward_budget'] = (int)$fee['reward_quantity'] * (int)$fee['reward_unit_price'];
        // E 額外項
        $fee['extra_budget'] = (int)$fee['rent_car_budget'] + (int)$fee['insurance_budget'] + (int)$fee['reward_budget'];

        if ($t04tb->realQuota < 40){
            // C-業務支出
            $fee['business_pay_budget'] = (int)$fee['business_pay_quantity'] * (int)$fee['business_pay_unit_price'];

            // A-業務費 = B + C + D + E
            $fee['service_fee_budget'] = (int)$fee['hourly_fee_budget'] + (int)$fee['business_pay_budget'] + (int)$fee['food_expenses_budget'] + (int)$fee['extra_budget'];
        }else{
            // A-業務費 = 人數 * 天數 * 單價 + E額外項
            $fee['service_fee_budget'] = ((int)$fee['service_fee_quantity'] * (int)$fee['service_fee_unit_price'] * (int)$fee['service_fee_days']) + $fee['extra_budget'] ;

            // C-業務支出 = A - B  - D - E 
            $fee['business_pay_budget'] = (int)$fee['service_fee_budget'] - (int)$fee['hourly_fee_budget'] - (int)$fee['food_expenses_budget'] - (int)$fee['extra_budget'];
        }

        return $fee;
    }
}