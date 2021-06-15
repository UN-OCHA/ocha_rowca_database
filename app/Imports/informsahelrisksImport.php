<?php

namespace App\Imports;

use App\inform_sahel_risk;
use Maatwebsite\Excel\Concerns\ToModel;
use Webpatser\Uuid\Uuid;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class informsahelrisksImport implements ToModel
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
            if(($row[0]!="Country")){
                $inform_sahel_risk = new inform_sahel_risk;
                $inform_sahel_risk->id_inform = (string)Uuid::generate();
                $inform_sahel_risk->inform_country=$row[0];
                $inform_sahel_risk->inform_admin0_pcode_iso3=$row[1];
                $inform_sahel_risk->inform_admin1=$row[2];
                $inform_sahel_risk->inform_admin1_pcode=$row[3];
                $inform_sahel_risk->inform_year=$row[4];
                $inform_sahel_risk->inf_111_food_insecurity_probab=($row[5]=='x') ? 0 : $row[5];
                $inform_sahel_risk->inf_112_physic_exposur_to_flood=($row[6]=='x') ? 0 : $row[6];
                $inform_sahel_risk->inf_113_land_degradation=($row[7]=='x') ? 0 : $row[7];
                $inform_sahel_risk->inf_114_droughts_proba_hist_imp=($row[8]=='x') ? 0 : $row[8];
                $inform_sahel_risk->inf_11_natural=($row[9]=='x') ? 0 : $row[9];
                $inform_sahel_risk->inf_121_political_violence=($row[10]=='x') ? 0 : $row[10];
                $inform_sahel_risk->inf_122_conflict_probability=($row[11]=='x') ? 0 : $row[11];
                $inform_sahel_risk->inf_12_human=($row[12]=='x') ? 0 : $row[12];
                $inform_sahel_risk->inf_1_hazard=($row[13]=='x') ? 0 : $row[13];
                $inform_sahel_risk->inf_211_developmnt_deprivation=($row[14]=='x') ? 0 : $row[14];
                $inform_sahel_risk->inf_212_inequality=($row[15]=='x') ? 0 : $row[15];
                $inform_sahel_risk->inf_213_aid_dependency=($row[16]=='x') ? 0 : $row[16];
                $inform_sahel_risk->inf_21_socio_eco_vulnerability=($row[17]=='x') ? 0 : $row[17];
                $inform_sahel_risk->inf_221_uprooted_people=($row[18]=='x') ? 0 : $row[18];
                $inform_sahel_risk->inf_222_health_conditions=($row[19]=='x') ? 0 : $row[19];
                $inform_sahel_risk->inf_223_children_u5=($row[20]=='x') ? 0 : $row[20];
                $inform_sahel_risk->inf_224_malnutrition=($row[21]=='x') ? 0 : $row[21];
                $inform_sahel_risk->inf_225_recent_shocks=($row[22]=='x') ? 0 : $row[22];
                $inform_sahel_risk->inf_226_food_security=($row[23]=='x') ? 0 : $row[23];
                $inform_sahel_risk->inf_227_other_vulnerable_groups=($row[24]=='x') ? 0 : $row[24];
                $inform_sahel_risk->inf_22_vulnerable_groups=($row[25]=='x') ? 0 : $row[25];
                $inform_sahel_risk->inf_2_vulnerability=($row[26]=='x') ? 0 : $row[26];
                $inform_sahel_risk->inf_311_drr=($row[27]=='x') ? 0 : $row[27];
                $inform_sahel_risk->inf_312_governance=($row[28]=='x') ? 0 : $row[28];
                $inform_sahel_risk->inf_31_institutional=($row[29]=='x') ? 0 : $row[29];
                $inform_sahel_risk->inf_321_communication=($row[30]=='x') ? 0 : $row[30];
                $inform_sahel_risk->inf_322_physical_infrastructure=($row[31]=='x') ? 0 : $row[31];
                $inform_sahel_risk->inf_323_access_to_health_care=($row[32]=='x') ? 0 : $row[32];
                $inform_sahel_risk->inf_32_infrastructure=($row[33]=='x') ? 0 : $row[33];
                $inform_sahel_risk->inf_3_lack_of_coping_capacity=($row[34]=='x') ? 0 : $row[34];
                $inform_sahel_risk->inf_0_risk=($row[35]=='x') ? 0 : $row[35];


                $inform_sahel_risk->save();
            }


        }
        
        
    }

}
