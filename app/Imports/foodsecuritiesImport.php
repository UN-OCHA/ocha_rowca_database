<?php

namespace App\Imports;

use App\food_security;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class foodsecuritiesImport implements ToModel
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

                $food_security = new food_security;
                $food_security->fs_id = (string)Uuid::generate();
                $food_security->fs_country=$row[0];
                $food_security->fs_admin0_pcode_iso3=$row[1];
                $food_security->fs_admin1= $row[2];
                $food_security->fs_admin1_pcode_iso3= $row[3];
                $food_security->fs_food_insecure_people=$row[4];
                $food_security->fs_severe_food_insecure_people=$row[5];
                $food_security->fs_modera_food_insecure_people=$row[6];
                $food_security->fs_date=$date;

                $food_security->save();
            }
        }
    }

}
