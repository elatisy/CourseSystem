<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PPTUrl;

class PPTController extends Controller
{
    private $PPTUrlTable;

    public function __construct(PPTUrl $PPTUrlTable) {
        $this->PPTUrlTable = $PPTUrlTable;
    }

    public function uploadPPT(Request $request) {
        $this->validate($request,[
            'name'          => 'required',
            'PPTUrl'        => 'required',
            'class_name'    => 'required'
        ]);

        $this->PPTUrlTable->insert([
            'name'          => $request->name,
            'url'           => $request->PPTUrl,
            'class_name'    => $request->class_name,
            'users_id'      => $request->users_id
        ]);

        return response([
            'code'  => 0
        ]);
    }

    public function getAllPPTs(Request $request) {
        $PPTs = $this->PPTUrlTable->where('users_id', $request->users_id)->get();

        $data =[];
        foreach ($PPTs as $PPT) {
            $data []= [
                'id'            => $PPT->id,
                'name'          => $PPT->name,
                'class_name'    => $PPT->class_name,
                'url'           => $PPT->url
            ];
        }

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }
}
