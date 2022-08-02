<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StoreSetup extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'store_setup';

    public static function generateDocNo() {
        $now = Carbon::now()->format('Ymd');

        $doc_no = '';
        while(empty($doc_no)) {
            $random_doc_no = $now.'@'.generateRandomNo();
            $check_doc = self::where('serial_no', $random_doc_no)->count();

            if ($check_doc == 0) {
                $doc_no = $random_doc_no;
            }
        }
        return $doc_no;
    }
}
