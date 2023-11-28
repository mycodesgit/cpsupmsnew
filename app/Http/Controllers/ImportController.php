<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payroll;
use App\Models\PayrollFile;
use App\Models\Status;
use App\Models\Campus;
use App\Models\Modify;
use App\Models\ModifyJo;
use App\Models\ModifyPartimeJo;
use App\Models\Employee;
use App\Models\Deduction;
use App\Models\DeductionJo;
use App\Models\DeductionPartimeJo;

class ImportController extends Controller
{

    public function importPayrollsTwo(Request $request, $payrollID, $statID){
        $stat = Status::find($statID);
        $emp_statname = $stat->status_name;
        $payroll = Payroll::find($payrollID);
        $pay_id = $payroll->id;
        $campID = $payroll->campus_id;
        $startDate = $payroll->payroll_dateStart;
        $endDate = $payroll->payroll_dateEnd;

        $empid = $request->emp_ID;

        if ($statID == 1) {
            $modeldeduct = 'Deduction';
            $modelmodify = 'modify';
            $dedtable = 'deductions';
        }elseif ($statID == 3) {
            $modeldeduct = 'DeductionPartimeJo';
            $modelmodify = 'ModifyPartimeJo';
            $dedtable = 'deduction_jos';
        }elseif ($statID == 4) {
            $modeldeduct = 'DeductionJo';
            $modelmodify = 'ModifyJo';
            $dedtable = 'deduction_jos';
        }

        $modelInstance = 'App\Models\\' . $modeldeduct;
        $modelInstance1 = 'App\Models\\' . $modelmodify;

        if($statID == 1 || $statID == 4){
            $hr_day = "Hours";
            $number_hours=$request->number_hours * 8;
            $days = $request->number_hours;
        }

        if($emp_statname == "Part-time/JO"){
            $hr_day = "Hours";
            $number_hours=$request->number_hours;
            $days = 0;
        }
        $employees = Employee::find($empid);
        $empOff=$employees->emp_dept;
        if($employees->partime_rate == 0){
            $salary = $employees->emp_salary;
            $total_sal = floatval(sprintf("%.2f",$salary * $days));
        }
        if($employees->partime_rate != 0){
            $salary = $employees->partime_rate;
            $total_sal = floatval(sprintf("%.2f",$salary * $number_hours));
        }

        if($statID == 1){
            $tax1 = "0.00";
            $rlip = round(($employees->emp_salary * 0.09), 2);
            if($employees->emp_salary >= 80000){
                $ph = 1600.00;
            }
            else{
                $ph = $employees->emp_salary * 0.02;
            }
        }

        if($statID == 3 || $statID == 4){
            $half = round(($employees->emp_salary / 2), 2);
            $tax1 = floatval(sprintf("%.2f",$half * 0.03));
        }

            $existing_record = PayrollFile::where('payroll_ID', $payrollID)
            ->where('emp_id', $request->emp_ID)
            ->where('camp_ID', $campID)
            ->where('stat_ID', $statID)
            ->where('startDate', $startDate)
            ->where('endDate', $endDate)
            ->first();

            if ($existing_record) {
                return redirect()->back()->with('error', 'Already Exist');  
            }
            else {
            $payrollFile = PayrollFile::create([
                'payroll_ID' => $payrollID,
                'emp_id' => $request->emp_ID,
                'emp_pos' => $employees->position,
                'sg' => $employees->sg_step,
                'salary_rate' => $salary,
                'number_hours' => $number_hours,
                'number_days' => $days,
                'hr_day' => $hr_day,
                'total_salary' => sprintf("%.2f", $total_sal),
                'startDate' => $startDate,
                'endDate' => $endDate,
                'camp_ID' => $campID,
                'stat_ID' => $statID,
            ]);
            
            $payrollID = $payrollFile->id;
            
			if($statID == 1){
                
                $deduction = $modelInstance::where('emp_id', $empid)->latest()->first();

                if($deduction){
                    $modelInstance::create([
                        'pay_id' => $pay_id,
                        'payroll_id' => $payrollID,
                        'emp_id' => $empid,
                        'tax2' => '0.00',
                        'add_sal_diff' => $deduction->add_sal_diff,
                        'add_nbc_diff' => $deduction->add_nbc_diff,
                        'add_step_incre' => $deduction->add_step_incre,
                        'eml' => '0.00',
                        'pol_gfal' => $deduction->pol_gfal,
                        'consol' => '0.00',
                        'ed_asst_mpl' => '0.00',
                        'loan' => '0.00',
                        'rlip' => $deduction->rlip,
                        'gfal' => '0.00',
                        'computer' => '0.00',
                        'mpl' => '0.00',
                        'prem' => $deduction->prem,
                        'calam_loan' => '0.00',
                        'mp2' => $deduction->mp2,
                        'philhealth' => $deduction->philhealth,
                        'holding_tax' => $deduction->holding_tax,
                        'lbp' => '0.00',
                        'cauyan' => '0.00',
                        'projects' => '0.00',
                        'nsca_mpc' => '0.00',
                        'med_deduction' => '0.00',
                        'grad_guarantor' => '0.00',
                        'cfi' => '0.00',
                        'csb' => '0.00',
                        'fasfeed' => $deduction->fasfeed,
                        'dis_unliquidated' => '0.00',
                    ]);
                }
                else{
                    $modelInstance::create([
                        'pay_id' => $pay_id,
                        'payroll_id' => $payrollID,
                        'emp_id' => $empid,
                        'rlip' => $rlip ?? '0.00',
                        'philhealth' => $ph ?? '0.00',
                        'fasfeed' => '100',
                    ]);
                }

                $data = [
                    ['column' => 'Column1', 'label' => 'Project', 'action' => 'Deduction', 'amount' => '0.00'],
                    ['column' => 'Column2', 'label' => 'Net MPC', 'action' => 'Deduction', 'amount' => '0.00'],
                    ['column' => 'Column3', 'label' => 'Graduate', 'action' => 'Deduction', 'amount' => '0.00'],
                    ['column' => 'Column4', 'label' => 'Philhealth', 'action' => 'Deduction', 'amount' => '0.00'],
                    ['column' => 'Column5', 'label' => 'Pag ibig', 'action' => 'Deduction', 'amount' => '0.00'],
                    ['column' => 'Column6', 'label' => 'GSIS', 'action' => 'Deduction', 'amount' => '0.00'],
                    ['column' => 'Column7', 'label' => 'CSB', 'action' => 'Deduction', 'amount' => '0.00'],
                ];

                foreach ($data as $item) {
                    $label = isset($item['label']) ? $item['label'] : null;
                    $modelInstance1::create([
                        'pay_id' => $pay_id,
                        'payroll_id' => $payrollID,
                        'off_id' => $empOff,
                        'column' => $item['column'],
                        'label'  => $label,
                        'action' => $item['action'],
                        'amount' => $item['amount'],
                    ]);
                }   
            }

            if($statID == 3 || $statID == 4){

                $modelInstance::create([
                    'pay_id'=> $pay_id,
                    'payroll_id'=> $payrollID,
                    'emp_id'=>$empid,
                    'tax1' => $tax1,
                ]);

                $data = [
                    ['column' => 'Column1', 'label' => 'Column1', 'action' => 'Additionals', 'amount' => '0.00'],
                    ['column' => 'Column2', 'label' => 'Column2', 'action' => 'Additionals', 'amount' => '0.00'],
                    ['column' => 'Column3', 'label' => 'Column3', 'action' => 'Additionals', 'amount' => '0.00'],
                    ['column' => 'Column4', 'label' => 'Column4', 'action' => 'Additionals', 'amount' => '0.00'],
                    ['column' => 'Column5', 'label' => 'Column5', 'action' => 'Additionals', 'amount' => '0.00'],
                ];

                foreach ($data as $item) {
                    $label = isset($item['label']) ? $item['label'] : null;
                    $modelInstance1::create([
                        'pay_id' => $pay_id,
                        'payroll_id' => $payrollID,
                        'off_id' => $empOff,
                        'column' => $item['column'],
                        'label'  => $label,
                        'action' => $item['action'],
                        'amount' => $item['amount'],
                    ]);
                } 

            }                  

            return redirect()->back()->with('success', 'Additionals successfully');  
        }
    }

}
