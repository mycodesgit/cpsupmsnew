<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deduction;
use App\Models\DeductionJo;
use App\Models\DeductionPartimeJo;
use App\Models\PayrollFile;
use App\Models\Modify;
use App\Models\ModifyJo;
use App\Models\ModifyPartimeJo;

class ModifyController extends Controller
{
    
    public function modifyShow(Request $request)
    {
        $id = $request->id;
        $statID = $request->stat;

        if ($statID == 1) {
            $modeldmody = 'Modify';
        } elseif ($statID == 3) {
            $modeldmody = 'ModifyPartimeJo';
        }elseif ($statID == 4) {
            $modeldmody = 'ModifyJo';
        }
        $modelInstance = 'App\Models\\' . $modeldmody;
        $modifyRecords = $modelInstance::where('payroll_id', $id)->get(); 
    
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $modifyRecords,
        ]);
    }

    public function modifyUpdate(Request $request)
    {
        $route = $request->curr_route;
        $payrollID = $request->id;
        $payrollID1 = $request->idd;

        if($route == "storepayroll"){
            $columns = [
                'Column1' => 'Column1',
                'Column2' => 'Column2',
                'Column3' => 'Column3',
                'Column4' => 'Column4',
                'Column5' => 'Column5',
                'Column6' => 'Column6',
                'Column7' => 'Column7',
            ];
            
            foreach ($columns as $column => $fieldName) {
                $modify = Modify::firstOrNew(['payroll_id' => $payrollID, 'column' => $column]);
                $modify->action = $request->{$fieldName . '_action'};
                $modify->amount = $request->{$fieldName . '_amount'};
                $modify->save();
                
                Modify::where(['pay_id' => $payrollID1, 'column' => $column])
                ->update([
                    'label' => $request->{$fieldName . '_label'} ?? '',
                    'affected' => $request->{$fieldName . '_affected'} ?? '',
                ]);

            }            

        }
        if ($route == "storepayroll-jo" || $route == "storepayroll-partime-jo") {
            $columns = [
                'Column1' => 'Column1',
                'Column2' => 'Column2',
                'Column3' => 'Column3',
                'Column4' => 'Column4',
                'Column5' => 'Column5'
            ];

            if ($route == "storepayroll-jo") {
                $modelded = "DeductionJo";
                $modeldmody = 'ModifyJo';
            } elseif ($route == "storepayroll-partime-jo") {
                $modelded = "DeductionPartimeJo";
                $modeldmody = 'ModifyPartimeJo';
            }

            $modeldedInstance = 'App\Models\\' . $modelded;
            $modelmodyInstance = 'App\Models\\' . $modeldmody;
        
            foreach ($columns as $column => $fieldName) {
                $modify = $modelmodyInstance::firstOrNew(['payroll_id' => $payrollID, 'column' => $column]);
                $modify->amount = $request->{$fieldName . '_amount'} ?? 0;
                $modify->save();

                $modelmodyInstance::where(['pay_id' => $payrollID1, 'column' => $column])
                ->update([
                    'label' => $request->{$fieldName . '_label'} ?? '',
                ]);
            }            

            $deduction = $modeldedInstance::where('payroll_id', $payrollID)->first();
            $tax2 = $deduction->tax2;
            if($tax2 != 0.00){
                $pfile = PayrollFile::find($payrollID);
                $modify = $modelmodyInstance::where('payroll_id', $payrollID);

                $totaladd = 0;
                if(isset($modify)){
                    $totaladd = $modify->sum('amount');
                
                    $tax2 = round(($pfile->salary_rate / 2) + ($totaladd) - ($deduction->add_less_abs + $deduction->less_late), 2); 
                    $tax1 = floatval(sprintf("%.2f",$tax2 * 0.03));
                    $tax2 = floatval(sprintf("%.2f",$tax2 * 0.02));

                    $modeldedInstance::where('payroll_id', $payrollID)->update([
                        'tax1' => $tax1,
                        'tax2' => $tax2,
                    ]);
                }
            }
        }
        
        return redirect()->back()->with('success', 'Updated successfully');
        
    }
      
}
