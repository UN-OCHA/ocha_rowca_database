<?php

namespace App\Imports;

use App\internally_displaced_people;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class internallydisplacedpersonsImport implements ToModel
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
                $UNIX_DATE = ($row[23] - 25569) * 86400;
                //$date = gmdate("d/m/Y", $UNIX_DATE);
                $date = gmdate("Y/m/d", $UNIX_DATE);

                $internally_displaced_people = new internally_displaced_people;
                $internally_displaced_people->idp_id = (string)Uuid::generate();
                $internally_displaced_people->idp_country=$row[0];
                $internally_displaced_people->idp_admin0_pcode=$row[1];
                $internally_displaced_people->idp_dest_admin1_name=$row[2];
                $internally_displaced_people->idp_dest_admin1_pcode=$row[3];
                $internally_displaced_people->idp_origin_admin1_name=$row[4];
                $internally_displaced_people->idp_origin_admin1_pcode=$row[5];
                $internally_displaced_people->idp_dest_admin2_name=$row[6];
                $internally_displaced_people->idp_dest_admin2_pcode=$row[7];
                $internally_displaced_people->idp_origin_admin2_name=$row[8];
                $internally_displaced_people->idp_origin_admin2_pcode=$row[9];
                $internally_displaced_people->idp_dest_admin3_name=$row[10];
                $internally_displaced_people->idp_dest_admin3_pcode=$row[11];
                $internally_displaced_people->idp_origin_admin3_name=$row[12];
                $internally_displaced_people->idp_origin_admin3_pcode=$row[13];
                $internally_displaced_people->idp_dest_admin4_name=$row[14];
                $internally_displaced_people->idp_dest_admin4_pcode=$row[15];
                $internally_displaced_people->idp_origin_admin4_name=$row[16];
                $internally_displaced_people->idp_origin_admin4_pcode=$row[17];
                $internally_displaced_people->idp_dest_settlement_name=$row[18];
                $internally_displaced_people->idp_dest_settlement_pcode=$row[19];
                $internally_displaced_people->idp_origin_settlement_name=$row[20];
                $internally_displaced_people->idp_origin_settlement_pcode=$row[21];
                $internally_displaced_people->idp_site_camp_name=$row[22];
                $internally_displaced_people->idp_total_individus=$row[22];
                $internally_displaced_people->idp_date=$date;
                $internally_displaced_people->idp_source=$row[24];

                $internally_displaced_people->save();
            }
        }
    }

}
