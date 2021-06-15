<?php

namespace App\Imports;

use App\caseload;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class caseloads_wca_Import implements ToModel
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
                $UNIX_DATE = ($row[9] - 25569) * 86400;
                $date = gmdate("Y/m/d", $UNIX_DATE);

                $caseload = new caseload;
                $caseload->id_caseload = (string)Uuid::generate();
                $caseload->caseload_country = $row[0];
                $caseload->admin0_pcode_iso3 = $row[1];
                $caseload->caseload_admin1_name = $row[2];
                $caseload->caseload_admin1_pcode = $row[3];
                $caseload->caseload_total_population = floatval($row[4]);
                $caseload->caseload_people_affected = floatval($row[5]);
                $caseload->caseload_people_in_need = floatval($row[6]);
                $caseload->caseload_people_targeted = floatval($row[7]);
                $caseload->caseload_people_reached = floatval($row[8]);
                $caseload->caseload_date = $date;
                $caseload->caseload_crise = "WCA";
                $caseload->caseload_source = isset($row[10]) ? $row[10] : null;
                $caseload->save();
            }
        }
        
        
    }

}
