<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public $timestamps = false;
    protected $table = 'districts';
    protected $visible = [
        // 'id', 'name', 'neighbourhoods'
        'id', 'name'
    ];

    // public function neighbourhoods()
    // {
    //     return $this->hasMany('App\Model\Neighbourhood');
    // }
}
