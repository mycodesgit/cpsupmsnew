<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollFile; 
use App\Models\Deduction;
use App\Models\Deductiontwo;
use App\Models\Campus;
use App\Models\Status;
use App\Models\Employee;
use App\Models\Modify;

class DeductionController extends Controller
{
    public function deductionsEdit(Request $request){
        $id = $request->id;
        $cat = $request->cat;
        $modes = $request->modes;
     
        $deductions = Deduction::where('payroll_id', $id)->select('*')->get();
        
        $payroll = PayrollFile::find($id);
        $statID = $payroll->stat_ID;
        $stat = Status::find($statID);
        $emp_statname = $stat->status_name;
    
        if($emp_statname == "Job Order" || $emp_statname == "Part-time"){
            return response()->json([
                'status'=>200,
                'types'=>'add',
                'empstat'=>'Job Order',
                'data'=>$deductions,
            ]);
        }
        else{
            return response()->json([
                'status'=>200,
                'types'=>'deduct', 
                'empstat'=>'Not JO',
                'data'=>$deductions,
            ]);
        }
    }
    
    public function deductionsUpdate(Request $request){
        $cat = $request->cat;
        $id = $request->payroll_id;
        $payroll = PayrollFile::find($id);
        $statID = $payroll->stat_ID;
        $emp_id = $payroll->emp_id;
        $employee = Employee::where('emp_ID', $emp_id)->first();
        $emp_salary = $payroll->salary_rate;
        $stat = Status::find($statID);
        $emp_statname = $stat->status_name;
        $tax2 = floatval(sprintf("%.2f", $request->tax2 * $payroll->total_salary));
        $tax2 = number_format($tax2, 2);
        
        $deduction = Deduction::where('payroll_id', $id)->first();
        
        $total_deduct = $request->eml + $request->pol_gfal + $request->consol + $request->ed_asst_mpl + $request->loan 
        + $request->rlip + $request->gfal + $request->mpl + $request->computer + $request->health + $request->prem + $request->philhealth + $request->holding_tax + $request->lbp 
        + $request->cauyan + $request->projects + $request->nsca_mpc + $request->med_deduction+ $request->grad_guarantor 
        + $request->cfi + $request->csb + $request->fasfeed + $request->dis_unliquidated + $request->add_less_abs;
        
        $additional = $deduction->add_sal_diff + $deduction->add_nbc_diff + $deduction->add_step_incre;
            
        $earn = $emp_salary + $additional - $request->add_less_abs;
        $rlip_left = round(($earn * 0.09),2);
        $philhealth = round(($earn * 0.02),2);

        if($earn >= 80000){
            $philhealth = 1600.00;
        }

        $net_regular = floatval(sprintf("%.2f",$emp_salary + $additional - $total_deduct, 2));

        $total_deduct = number_format($total_deduct, 2);
        $net_regular = number_format($net_regular, 2);

        if($tax2 <= 0){ 
            $tax2 = '0.00';
        }

        if($emp_statname == "Job Order" || $emp_statname == "Part-time"){

            $modify = Modify::where('payroll_id', $id);
            $totaladd = 0;
            if(isset($modify)){
                $totaladd = $modify->sum('amount');
            }
            $tax2 = $request->tax_two;
            if($tax2 != 0.00 || $request->twocheckbox == 1){
                $tax2 = round(($emp_salary / 2) + ($totaladd) - ($request->add_less_abs + $request->less_late), 2); 
                $tax2 = floatval(sprintf("%.2f",$tax2 * 0.02));
            }
            if($tax2 != 0.00 && $request->twocheckbox != 1){
                $tax2 = '0.00';
            }

            $half = round(($emp_salary / 2) + ($totaladd) - ($request->add_less_abs + $request->less_late), 2);            
            $tax1 = floatval(sprintf("%.2f",$half * 0.01));

            Deduction::where('payroll_id', $id)->update([
                'tax1' => $tax1,
                'tax2' => $tax2,
                'projects' => $request->projects,
                'nsca_mpc' => $request->nsca_mpc,
                'grad_guarantor' => $request->grad_guarantor,
                'add_less_abs' => $request->add_less_abs,
                'less_late' => $request->less_late,
            ]);
        }

        else{
            Deduction::where('payroll_id', $id)->update([
                'tax2' => '0.00',
                'eml' => $request->eml,
                'pol_gfal' => $request->pol_gfal,
                'consol' => $request->consol,
                'ed_asst_mpl' => $request->ed_asst_mpl,
                'loan' => $request->loan,
                'rlip' => $rlip_left,
                'gfal' => $request->gfal,
                'computer' => $request->computer,
                'health' => $request->health,
                'mpl' => $request->mpl,
                'prem' => $request->prem,
                'calam_loan' => $request->calam_loan,
                'mp2' => $request->mp2,
                'house_loan' => $request->house_loan,
                'philhealth' => $philhealth,
                'holding_tax' => $request->holding_tax,
                'lbp' => $request->lbp,
                'cauyan' => $request->cauyan,
                'projects' => $request->projects,
                'nsca_mpc' => $request->nsca_mpc,
                'med_deduction' => $request->med_deduction,
                'grad_guarantor' => $request->grad_guarantor,
                'cfi' => $request->cfi,
                'csb' => $request->csb,
                'fasfeed' => $request->fasfeed,
                'dis_unliquidated' => $request->dis_unliquidated,
                'add_less_abs' => $request->add_less_abs,
                'add_less_abs1' => $request->add_less_abs1,
                'less_late' => $request->less_late,
            ]);
                
        }
        
        return redirect()->back()->with('success', 'Deductions updated successfully');    
    }

    public function additionalUpdate(Request $request){
        $cat = $request->cat;
        $id = $request->payroll_id;
        $payroll = PayrollFile::find($id);
        $statID = $payroll->stat_ID;
        $emp_id = $payroll->emp_id;
        $employee = Employee::where('emp_ID', $emp_id)->first();
        $emp_salary = $payroll->salary_rate;
        $stat = Status::find($statID);
        $emp_statname = $stat->status_name;
        
        $deduction = Deduction::where('payroll_id', $id)->first();
        
        $total_deduct = $deduction->eml + $deduction->pol_gfal + $deduction->consol + $deduction->ed_asst_mpl + $deduction->loan 
        + $deduction->rlip + $deduction->mpl + $deduction->prem + $deduction->philhealth + $deduction->holding_tax + $deduction->lbp 
        + $deduction->cauyan + $deduction->projects + $deduction->nsca_mpc + $deduction->med_deduction+ $deduction->grad_guarantor 
        + $deduction->cfi + $deduction->csb + $deduction->fasfeed + $deduction->dis_unliquidated + $deduction->add_less_abs;

        $additional = $request->add_sal_diff + $request->add_nbc_diff + $request->add_step_incre;

        $earn = ($emp_salary + $additional) - $deduction->add_less_abs;
        $rlip_left = round(($earn * 0.09),2);

        $philhealth = round(($earn * 0.02),2);

        if($earn >= 80000){
            $philhealth = 1600.00;
        }
        
        $net_regular = floatval(sprintf("%.2f",$emp_salary + $additional - $total_deduct, 2));
        
        Deduction::where('payroll_id', $id)->update([
            'add_sal_diff' => $request->add_sal_diff,
            'add_nbc_diff' => $request->add_nbc_diff,
            'add_step_incre' => $request->add_step_incre,
            'rlip' => $rlip_left,
            'philhealth' => $philhealth,
        ]);
        
        return redirect()->back()->with('success', 'Additionals updated successfully');  

    }
}
