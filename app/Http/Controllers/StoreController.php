<?php

namespace App\Http\Controllers;

use App\Http\Services\BaseService;
use App\Models\StoreDetail;
use App\Models\StoreSetup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    private $base;

    public function __construct(
        BaseService $base
    )
    {
        $this->base = $base;
    }

    public function createStore(Request $request) {
        $inputs = $request->all();

        $baseRules = [
            'store_name' => 'required',
            'branch_name' => 'sometimes',
            'latitude' => 'required',
            'longitude' => 'required',
            'city' => 'required',
            'state' => 'required',
            'full_addr' => 'required',
        ];

        $validationResult = $this->base->validateInput($baseRules, $inputs);

        if ($validationResult['status'] == "0") {
            return ["rst" => "0", "msg" => $validationResult['msg']];
        }

        try {
            $now = Carbon::now();
            $doc_no = StoreSetup::generateDocNo();
            $insert = new StoreSetup();
            $insertDetail = new StoreDetail();

            $coordinate['lat'] = $inputs['latitude'];
            $coordinate['long'] = $inputs['longitude'];

            $insert->serial_no = $doc_no;
            $insert->rate = '5';
            $insert->location = json_encode($coordinate);
            $insert->operation_status = 'A';
            $insert->created_at = $now;

            if (!$insert->save()) {
                return ["rst" => "0", "msg" => 'create_store_failed', "data" => []];
            }

            $insertDetail->serial_no = $doc_no;
            $insertDetail->store_name = $inputs['store_name'];
            $insertDetail->store_subname = $inputs['branch_name'];
            $insertDetail->store_address = strtoupper($inputs['full_addr']);
            $insertDetail->store_city = strtoupper($inputs['city']);
            $insertDetail->store_state = strtoupper($inputs['state']);
            $insertDetail->store_country = 'Malaysia';

            if (!$insertDetail->save()) {
                return ["rst" => "0", "msg" => 'create_store_failed', "data" => []];
            }

            return ["rst" => "1", "msg" => 'create_store_success', "data" => []];
        } catch (\Exception $e) {
            return ["rst" => "0", "msg" => $e->getMessage(), "data" => []];
        }
    }

    public function getStore(Request $request) {
        $inputs = $request->all();

        $baseRules = [
          'page' => 'required',
          'limit' => 'required',
          'category' => 'sometimes',
          'store_name' => 'sometimes',
          'status' => 'sometimes',
        ];

        $validationResult = $this->base->validateInput($baseRules, $inputs);

        if ($validationResult['status'] == "0") {
            return ["rst" => "0", "msg" => $validationResult['msg']];
        }

        try {
            $store_query = StoreSetup::join('store_detail', 'store_detail.serial_no', 'store_setup.serial_no')
                ->where('store_setup.operation_status', 'A');

            if (!empty($inputs['store_name'])) {
                $store_query = $store_query->where('store_detail.store_name', 'LIKE', '%'.strtolower($inputs['store_name']).'%');
            }

            $store_list = $store_query->get([
               DB::raw("store_setup.serial_no as store_no"),
               DB::raw("store_detail.store_name"),
               DB::raw("store_setup.rate as rating"),
            ]);

            if (!isset($inputs['page']) || !isset($inputs['limit'])) {
                $inputs['page'] = 1;
                $inputs['limit'] = 10;
            }

            $return = $this->base->processPaginationFormatFromArrayFormat(
                count($store_list) > 0 ? $store_list->toArray() : [], $inputs['page'], $inputs['limit']);

            return ["rst" => "1", "msg" => 'success', "data" => $return];
        } catch (\Exception $e) {
            return ["rst" => "0", "msg" => $e->getMessage(), "data" => []];
        }
    }
}
