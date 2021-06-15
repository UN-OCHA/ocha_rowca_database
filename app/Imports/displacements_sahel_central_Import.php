<?php

namespace App\Imports;

use App\displacement;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class displacements_sahel_central_Import implements ToModel
{
    public function model (array $row)
    {
        if (!isset($row[0])) {
            return null;
        }else{
            if($row[0]!="Country"){
                $UNIX_DATE = ($row[7] - 25569) * 86400;
                //$date = gmdate("d/m/Y", $UNIX_DATE);
                $date = gmdate("Y/m/d", $UNIX_DATE);

                $displacement = new displacement;
                $displacement->dis_id = (string)Uuid::generate();
                $displacement->dis_country=$row[0];
                $displacement->dis_admin0_pcode=$row[1];
                $displacement->dis_admin1_name=$row[2];
                $displacement->dis_admin1_pcode=$row[3];
                $displacement->dis_type=$row[4];
                $displacement->dis_value=$row[5];
                $displacement->dis_source=$row[6];
                $displacement->dis_date=$date;
                $displacement->dis_crise='SAHC';

                $displacement->save();
            }
        }
    }

}
