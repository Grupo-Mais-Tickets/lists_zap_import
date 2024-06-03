<?php

namespace App\Domains\Exports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lists;

class ExportsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            // Validação do arquivo para garantir que seja CSV ou XLS
            $this->validate($request, [
                'export' => 'required|file|mimes:csv,xls,xlsx',
                'list_id' => 'required'
            ]);

            $file = $request->file('export');

            $destinationPath = public_path('exports');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->move($destinationPath, $fileName);

            $list = (new Lists())->where('id', $request->list_id)
                ->first();

            if ($list) {
                $list->status = 'processando';
                $list->save();
                dispatch(new \App\Jobs\ExtractData($destinationPath . '/' . $fileName, $list));
            }

            return response()->json(['message' => 'File uploaded successfully and job dispatched', 'file' => $fileName], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

}
