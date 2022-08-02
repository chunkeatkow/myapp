<?php

namespace App\Http\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class BaseService
{
    public function validateInput($base_rules, $arrData)
    {
        $messages = [
            'required'      => 'please_enter_'.':attribute',
            'same'          => 'the_:attribute_and_:other_must_match',
            'size'          => 'the_:attribute_must_be_exactly_:size',
            'between'       => 'the_:attribute_must_be_between_:min_-_:max',
            'in'            => 'the_:attribute_must_be_one_of_the_following_types_:values',
            'exists'        => 'the_:attribute'.'_not_exists',
            'unique'        => 'the_:attribute'.'_exists',
            'email'         => 'the_:attribute_invalid_email_format',
            'min'           => 'the_'.':attribute'.'_min_character_is_:min',
            'max'           => 'the_'.':attribute'.'_max_character_is_:max',
            'numeric'       => 'the_'.':attribute_must_be_number',
            'integer'       => 'the_'.':attribute_must_be_integer',
            'date_format'   => 'invalid_date_format_:attribute',
            'regex'         => 'invalid_:attribute_format',
            'alpha_num'     => 'only_alpha_numeric_is_allow_for_the_:attribute',
            'different'     => 'invalid_'.':attribute',
            'required_with' => 'please_enter_'.':attribute',
            'unique'        => 'invalid_'.':attribute',
        ];

        $validator = Validator::make($arrData, $base_rules, $messages);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $key => $value) {
                return $arrResult = ['status' => "0", 'msg' => str_replace(" ", "_", strtolower($value))];
            }
        }

        return $arrResult = ['status' => "1", 'msg' => "success"];
    }

    public function processPaginationFormatFromArrayFormat($data, $currentPage, $itemPerPage){

        // start example of array format
        // $arrNotification[0]['id'] = 1;
        // $arrNotification[0]['title'] = 'hello';
        // $arrNotification[0]['msg'] = 'welcome to here';
        // $arrNotification[0]['url'] = 'www.pagination.com';
        // $arrNotification[0]['created_at'] = '2019-03-22 13:00:00';
        // $arrNotification[0]['total_cost_sum_current_page'] = 224.324; calculate all the total value for _sum_current_page variable for current pages, use _sum_current_page to run this function
        // $arrNotification[0]['total_cost_sum_all_page'] = 224.324; calculate all the total value for _sum_all_page variable for all the pages, use _sum_all_page to run this function
        // end example of array format

        $totNotification = $lastPage = 0;
        $newCurrentPageItems = [];

        if (count($data) > 0){
            $collection = collect($data);
            $currentPageItems = $collection->slice(($currentPage * $itemPerPage) - $itemPerPage, $itemPerPage)->all();
            $paginatedItems= new LengthAwarePaginator($currentPageItems , count($collection), $itemPerPage);
            $totNotification = $paginatedItems->total();
            $lastPage = $paginatedItems->lastPage();

            $count = 0;
            $keyItem = [];
            foreach ($currentPageItems as $currentPageItemsK => $currentPageItemsV){
                $newKeyItem = array_keys($currentPageItemsV);
                $keyItem = array_merge($newKeyItem, $keyItem);
            }
            $keyItem = array_unique($keyItem);
            $newKeyItem = [];
            foreach($keyItem as $keyItemK => $keyItemV){
                $newKeyItem[] = $keyItemV;
            }
            $keyItem = $newKeyItem;

            foreach ($currentPageItems as $currentPageItemsK => $currentPageItemsV){
                foreach ($keyItem as $keyItemK => $keyItemV){
                    if (array_key_exists($keyItemV, $currentPageItemsV)){
                        $newCurrentPageItems[$count][$keyItemV] = $currentPageItemsV[$keyItemV];
                    }
                }
                $count++;
            }

            $arrTrimKey = $arrReturnData = [];
            foreach ($keyItem as $keyItemK2 => $keyItemV2){

                // start calculate all the total value for _sum variable for current pages
                if(strpos($keyItemV2,"_sum_current_page_2_dec") !== false){
                    $arrTrimKey[] = ['_sum_current_page_2_dec' => $keyItemV2];
                    $total = 0;
                    foreach ($newCurrentPageItems as $newCurrentPageKey => $newCurrentPageValue){
                        if (array_key_exists($keyItemV2, $newCurrentPageValue)){
                            $total = $newCurrentPageValue[$keyItemV2] + $total;
                        }
                    }

                    $convertedDecimal = number_format($total,4,'.','');
                    $total = substr($convertedDecimal, 0, -2);
                    $arrReturnData["totalCurrentPage".ucfirst(str_replace("_sum_current_page_2_dec","", $keyItemV2))][] = "$total";
                }
                // start calculate all the total value for _sum variable for current pages

                // start calculate all the total value for _tot_sum variable for all the pages
                if(strpos($keyItemV2,"_sum_all_page_2_dec") !== false){
                    $arrTrimKey[] = ['_sum_all_page_2_dec' => $keyItemV2];
                    $total_sum = 0;
                    foreach ($data as $dataK => $dataV){
                        if (array_key_exists($keyItemV2, $dataV)){
                            $total_sum = $dataV[$keyItemV2] + $total_sum;
                        }
                    }
//                    $arrReturnData["total".ucfirst(str_replace("_sum_all_page_2_dec","", $keyItemV2))] = "$total_sum";
                    $convertedDecimal = number_format($total_sum,4,'.','');
                    $total_sum = substr($convertedDecimal, 0, -2);
                    $arrReturnData["total".ucfirst(str_replace("_sum_all_page_2_dec","", $keyItemV2))][] = "$total_sum";
                }
                // end calculate all the total value for _tot_sum variable for all the pages

                // start calculate all the total value for _sum variable for current pages
                if(strpos($keyItemV2,"_sum_current_page_3_dec") !== false){
                    $arrTrimKey[] = ['_sum_current_page_3_dec' => $keyItemV2];
                    $total = 0;
                    foreach ($newCurrentPageItems as $newCurrentPageKey => $newCurrentPageValue){
                        if (array_key_exists($keyItemV2, $newCurrentPageValue)){
                            $total = $newCurrentPageValue[$keyItemV2] + $total;
                        }
                    }

                    $convertedDecimal = number_format($total,5,'.','');
                    $total = substr($convertedDecimal, 0, -2);
                    $arrReturnData["totalCurrentPage".ucfirst(str_replace("_sum_current_page_3_dec","", $keyItemV2))][] = "$total";
                }
                // start calculate all the total value for _sum variable for current pages

                // start calculate all the total value for _tot_sum variable for all the pages
                if(strpos($keyItemV2,"_sum_all_page_3_dec") !== false){
                    $arrTrimKey[] = ['_sum_all_page_3_dec' => $keyItemV2];
                    $total_sum = 0;
                    foreach ($data as $dataK => $dataV){
                        if (array_key_exists($keyItemV2, $dataV)){
                            $total_sum = $dataV[$keyItemV2] + $total_sum;
                        }
                    }
//                    $arrReturnData["total".ucfirst(str_replace("_sum_all_page_2_dec","", $keyItemV2))] = "$total_sum";
                    $convertedDecimal = number_format($total_sum,5,'.','');
                    $total_sum = substr($convertedDecimal, 0, -2);
                    $arrReturnData["total".ucfirst(str_replace("_sum_all_page_3_dec","", $keyItemV2))][] = "$total_sum";
                }
                // end calculate all the total value for _tot_sum variable for all the pages

                // start calculate all the total value for _sum variable for current pages
                if(strpos($keyItemV2,"_sum_current_page_8_dec") !== false){
                    $arrTrimKey[] = ['_sum_current_page_8_dec' => $keyItemV2];
                    $total = 0;
                    foreach ($newCurrentPageItems as $newCurrentPageKey => $newCurrentPageValue){
                        if (array_key_exists($keyItemV2, $newCurrentPageValue)){
                            $total = $newCurrentPageValue[$keyItemV2] + $total;
                        }
                    }

                    $convertedDecimal = number_format($total,10,'.','');
                    $total = substr($convertedDecimal, 0, -2);
                    $arrReturnData["totalCurrentPage".ucfirst(str_replace("_sum_current_page_8_dec","", $keyItemV2))][] = "$total";
                }
                // start calculate all the total value for _sum variable for current pages

                // start calculate all the total value for _tot_sum variable for all the pages
                if(strpos($keyItemV2,"_sum_all_page_8_dec") !== false){
                    $arrTrimKey[] = ['_sum_all_page_8_dec' => $keyItemV2];
                    $total_sum = 0;
                    foreach ($data as $dataK => $dataV){
                        if (array_key_exists($keyItemV2, $dataV)){
                            $total_sum = $dataV[$keyItemV2] + $total_sum;
                        }
                    }
//                    $arrReturnData["total".str_replace("_sum_all_page_8_dec","", $keyItemV2)] = "$total_sum";
                    $convertedDecimal = number_format($total_sum,10,'.','');
                    $total_sum = substr($convertedDecimal, 0, -2);
                    $arrReturnData["total".str_replace("_sum_all_page_8_dec","", $keyItemV2)][] = "$total_sum";
                }
                // end calculate all the total value for _tot_sum variable for all the pages

                // start process for _2_dec
                if(strpos($keyItemV2,"_2_dec") !== false){
                    $arrTrimKey[] = ['_2_dec' => $keyItemV2];
                    foreach ($newCurrentPageItems as $newCurrentPageItemsK => $newCurrentPageItemsV){
                        if (array_key_exists($keyItemV2, $newCurrentPageItemsV)){
                            $convertedDecimal = number_format($newCurrentPageItemsV[$keyItemV2],10,'.','');
                            $newCurrentPageItems[$newCurrentPageItemsK][$keyItemV2] = substr($convertedDecimal, 0, -2);
                        }
                    }
                }
                // end process for _8_dec

                // start process for _8_dec
                if(strpos($keyItemV2,"_8_dec") !== false){
                    $arrTrimKey[] = ['_8_dec' => $keyItemV2];
                    foreach ($newCurrentPageItems as $newCurrentPageItemsK => $newCurrentPageItemsV){
                        if (array_key_exists($keyItemV2, $newCurrentPageItemsV)){
                            $convertedDecimal = number_format($newCurrentPageItemsV[$keyItemV2],10,'.','');
                            $newCurrentPageItems[$newCurrentPageItemsK][$keyItemV2] = substr($convertedDecimal, 0, -2);
                        }
                    }
                }
                // end process for _8_dec

            }
            if (count($arrReturnData) > 0){
                foreach ($arrReturnData as $arrReturnDataK => $arrReturnDataV){
                    $totalSum = array_sum($arrReturnDataV);
                    $arrReturnData[$arrReturnDataK] = "$totalSum";
                }
            }

            if (count($arrTrimKey) > 0){
                foreach ($arrTrimKey as $arrTrimKeyK => $arrTrimKeyV){
                    foreach ($arrTrimKeyV as $arrTrimKeyVK => $arrTrimKeyVV){
                        foreach ($newCurrentPageItems as $newCurrentPageItemsK => $newCurrentPageItemsV) {
                            if (isset($newCurrentPageItemsV[$arrTrimKeyVV]) == true) {
                                $newKey = str_replace("_sum_all_page_2_dec", "", $arrTrimKeyVV);
                                $newKey2 = str_replace("_sum_current_page_2_dec", "", $arrTrimKeyVV);
                                $newKey3 = str_replace("_sum_all_page_8_dec", "", $arrTrimKeyVV);
                                $newKey4 = str_replace("_sum_current_page_8_dec", "", $arrTrimKeyVV);
                                $newKey5 = str_replace("_sum_all_page_3_dec", "", $arrTrimKeyVV);
                                $newKey6 = str_replace("_sum_current_page_3_dec", "", $arrTrimKeyVV);
                                $newKey7 = str_replace("_2_dec", "", $arrTrimKeyVV);
                                $newKey8 = str_replace("_8_dec", "", $arrTrimKeyVV);
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey2] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey3] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey4] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey5] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey6] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey7] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                $newCurrentPageItems[$newCurrentPageItemsK][$newKey8] = $newCurrentPageItemsV[$arrTrimKeyVV];
                                unset($newCurrentPageItems[$newCurrentPageItemsK][$arrTrimKeyVV]);
                            }

                        }
                    }
                }
            }

        }

        $countNewCurrentPageItems= count($newCurrentPageItems);
        $arrReturnData['totalPageItems'] = "$totNotification";
        $arrReturnData['currentPage'] = "$currentPage";
        $arrReturnData['perPage'] = "$itemPerPage";
        $arrReturnData['totalPage'] = "$lastPage";
        $arrReturnData['totalCurrentPageItems'] = "$countNewCurrentPageItems";
        $arrReturnData['currentPageItems'] = $newCurrentPageItems;

        return $arrReturnData;
    }
}
