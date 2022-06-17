<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAds extends Model
{
    use HasFactory;

    static $usAccount = array(
        'Vanh 2.3 V301 Bang Will',
        'Vanh 2.2 V301 Bang Will',
        'Vanh 1.3 V301 Bang Will',
        'V301 2.1 Bang Will 2',
        'V301 1.1 Bang Will',
        'Phong 16.7 US V116 Created'
    );

    static $auAccount = array(
        'Phong 16.1 V31 AU4 Martina',
        'Phong 16.2 V31 AU8 Martina',
        'Phong 16.3 V31 AU9 Martina',
        'Phong 16.4 V31 AU4 Martina V38Cre',
        'Phong 16.5 V31 AU4 Martina V38Cre',
    );

    static $deAccount = array(
        'Phong 16.6 DE V38Cre'
    );

    static $usAccountIds = array(
        '2978647975730170',
        '612291543439190',
        '309846854338542',
        '588068822423832',
        '651874502834964',
        '748598509494241'
    );

    static $auAccountIds = array(
        '209267284523548',
        '4065060523598849',
        '199757128777881',
        '619094789457793',
        '333511255213931',
    );

    static $deAccountIds = array(
        '697732767946862'
    );
}
