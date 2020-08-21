<?php
namespace App\Services;

use App\Repositories\PollRepository;
use App\Models\T78tb;


class PollService
{
    /**
     * PollService constructor.
     * @param PollRepository $pollRpository
     */
    public function __construct(PollRepository $pollRpository)
    {
        $this->pollRpository = $pollRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPollList($queryData = [])
    {
        return $this->pollRpository->getPollList($queryData);
    }

    /**
     * 選項內容新增編輯刪除處理
     *
     * @param $request
     * @param $serno
     */
    public function updateAnswers($request, $serno)
    {
        $answers = $request->input('answers');
        $acts = $request->input('act');
        $id = $request->input('id');

        if ( ! is_array($acts)) {

            return;
        }

        foreach ($acts as $key => $act) {

            switch ($act)
            {
                case 'create':
                    // 新增
                    $data = array(
                        'serno' => $serno,
                        'answers' => $answers[$key],
                        'checknum' => 0,
                    );

                    $result = T78tb::create($data);
                    break;

                case 'update':
                    // 更新
                    $data['answers'] = $answers[$key];
                    T78tb::where('id', $id[$key])->update($data);
                    break;

                case 'delete':
                    // 刪除
                    if ($id[$key]) {

                        T78tb::where('id', $id[$key])->delete();
                    }
                    break;
            }
        }
    }
}
