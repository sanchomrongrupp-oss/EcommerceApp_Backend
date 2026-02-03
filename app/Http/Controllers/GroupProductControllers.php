<?php

namespace App\Http\Controllers;

use App\Models\GroupProduct;
use Illuminate\Http\Request;

class GroupProductControllers extends Controller
{
    public function addgrouppd(){
        $group_product = GroupProduct::all();
        return response()->json([
            'success' => true,
            'data' => $group_product
        ], 200);
    }
}
