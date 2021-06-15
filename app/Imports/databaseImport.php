<?php

namespace App\Imports;

use App\caseload;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class caseloadsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model (array $row)
    {
        if (!isset($row[0])) {
            return null;
        }else{
            if($row[0]!="Country"){
                $caseload = new caseload;
                $caseload->id_caseload = (string)Uuid::generate();
                $caseload->caseload_country = $row[0];
                $caseload->caseload_total_population = $row[1];
                $caseload->caseload_people_affected = $row[2];
                $caseload->caseload_people_in_need = $row[3];
                $caseload->caseload_people_targeted = $row[4];
                $caseload->caseload_people_reached = $row[5];
                $caseload->caseload_date = $row[6];
                $caseload->save();
            }
            
        /*$rowIndex = $row->getIndex();
        $row      = $row->toArray();

        $caseload = caseload::Create([
            'ID_CASELOAD' => $row[1],
        ]);*/

        }
        
        
    }

}
