<?php

namespace App\Imports;

use App\nutrition;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class nutritionsImport implements ToModel
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
                $UNIX_DATE = ($row[7] - 25569) * 86400;
                $date = gmdate("Y/m/d", $UNIX_DATE);

                $nutrition = new nutrition;
                $nutrition->nut_id = (string)Uuid::generate();
                $nutrition->nut_country=$row[0];
                $nutrition->nut_admin0_pcode=$row[1];
                $nutrition->nut_admin1= $row[2];
                $nutrition->nut_admin1_pcode= $row[3];
                $nutrition->nut_sam=$row[4];
                $nutrition->nut_gam=$row[5];
                $nutrition->nut_mam=$row[6];
                $nutrition->nut_date=$date;
                $nutrition->nut_source=isset($row[8]) ? $row[8] : null;

                $nutrition->save();
            }
        }
    }

}
