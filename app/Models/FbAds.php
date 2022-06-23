<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAds extends Model
{
    use HasFactory;

    static $usAccountIds = array(
        '2978647975730170',
        '612291543439190',
        '309846854338542',
        '588068822423832',
        '651874502834964',
        '748598509494241',
        '1038512286982822',
        '300489508749827',
        '977262739875449'
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
