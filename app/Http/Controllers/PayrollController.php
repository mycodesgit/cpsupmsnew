<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Payroll; 
use App\Models\Campus; 
use App\Models\Office;
use App\Models\Status;
use App\Models\PayrollFile;
use App\Models\Employee;
use App\Models\Deduction;
use App\Models\DeductionJo;
use App\Models\Code;
use App\Models\Modify; 
use App\Models\ModifyJo;
use PDF;

class PayrollController extends Controller
{
    
    public function viewPayroll($campId)
    {
        try {
            $role = auth()->user()->role;
            $userid = auth()->user()->id;
            $campId = Crypt::decrypt($campId);
    
            if ($role == "Administrator" || $role == "Payroll Administrator") {
                $status = Status::all();
                $camp = Campus::all();

            } elseif ($role == "Payroll Extension" && auth()->user()->campus_id != $campId) {
                throw new \Exception('You do not have permission to access this page');
            } else {
                $status = Status::where('status_name', '!=', 'Regular')->get();
                $camp = Campus::find(auth()->user()->campus_id)->get();
            }

            $pays = Payroll::join('campuses', 'payrolls.campus_id', '=', 'campuses.id')
            ->join('statuses', 'payrolls.stat_id', '=', 'statuses.id')
            ->join('users', 'payrolls.user_id', '=', 'users.id')
            ->select('payrolls.*', 'users.id as userid', 'users.lname', 'users.fname', 'campuses.campus_name', 'statuses.status_name')
            ->where('payrolls.campus_id', $campId)
            ->get();

            return view("payroll.viewPayroll", compact('camp', 'status', 'campId', 'pays'));

        } catch (\Exception $e) { 
            return redirect()->back()->with('error', 'You do not have permission to access this page');  
        }
    } 

    public function createPayroll(Request $request){
        $campID = $request->input('campID');
        $userid = auth()->user()->id;
        //$id = $request->payrollID;
        $validator = Validator::make($request->all(), [
            'statName'=>'required',
            'PayrollDateStart'=>'required',
            'PayrollDateEnd'=>'required',
            'number_days'=>'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            //Insert data into database 
            $payrollID = DB::table('payrolls')->insertGetId([
                'stat_id' => $request->input('statName'),
                'campus_id' => $request->input('campID'),
                'number_days' => $request->input('number_days'),
                'fund' => $request->input('statName') == 4 ? $request->input('fund') : null,
                'payroll_dateStart' => $request->input('PayrollDateStart'),
                'payroll_dateEnd' => $request->input('PayrollDateEnd'),
                'user_id'=>$userid,
            ]);            


            if ($request->input('statName') == 1) {
                $codes = [
                    ['code_for' => 'Regular', 'code_name' => 'GSIS Payable', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Due to PAG-IBIG(MPL)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Due to PAG-IBIG(Premium)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Due to PhilHealth', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Due to BIR', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(CSB)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(CFI)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(LBP)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(NSCA MPC)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(Projects)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(Grad School)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(FASFED)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(Absences)', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Due to Officers & Employee', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Regular', 'code_name' => 'Due to Officers & Employee', 'code' => '', 'status' => 'on'],
                ];
                
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    DB::table('codes')->insert($code);
                }
            }

            elseif ($request->input('statName') == 2) {
                $codes = [
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time', 'code_name' => 'Due to Bir', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                ];
            
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    DB::table('codes')->insert($code);
                }
            }            
            

            if ($request->input('statName') == 3){
                $codes = [
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => 'on'],
                ];
            
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    DB::table('codes')->insert($code);
                }
            }
            

            if ($request->input('statName') == 4) {
                $fund = $request->input('fund');
                if ($fund == "Income") {
                    $payable = "Labor and Wages";
                    $bank = "Cash in Bank-LC, LBP";
                    $bankcode = "10102020 24";
                } elseif ($fund == "Yearbook") {
                    $payable = "Other Payable";
                    $bank = "Cash in Bank";
                    $bankcode = "10102020 24";
                } else {
                    $payable = "Labor and Wages";
                    $bank = "Cash MDS-Regular";
                    $bankcode = "10104040 00";
                }
                
                $codes = [
                    ['code_for' => 'Job Order', 'code_name' => $payable, 'code' => '50216010 01'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable (NSCA Coop)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Due to BIR (1%)', 'code' => '20201010 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Due to BIR (2%)', 'code' => '20201010 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Receivable', 'code' => '10305990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable(NSCA MPC)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable(Grad Sch.)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable(Project)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => $bank, 'code' => $bankcode],
                ];                
            
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    $code['status'] = 'on';
                    DB::table('codes')->insert($code);
                }
            }
             
            return redirect()->back()->with('success', 'Payroll created successfully');  

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . 'You do not have permission to access this page'); 
        }
    }

    public function deletePayroll($id){
        $payroll = Payroll::find($id);
        $PayrollFile = PayrollFile::where('payroll_ID', $id)->first();
    
        if ($PayrollFile) {
            $id1 = $PayrollFile->id;
    
            $statID = $PayrollFile->stat_ID;
    
            if ($statID == 1) {
                $dedModel = 'Deduction';
                $modifyModel = 'Modify';
            }elseif ($statID == 3) {
                $dedModel = 'DeductionPartimeJo';
                $modifyModel = 'ModifyPartimeJo';
            }
            elseif ($statID == 4) {
                $dedModel = 'DeductionJo';
                $modifyModel = 'ModifyJo';
            }
    
            $dedModelInstance = 'App\Models\\' . $dedModel;
            $modifyModelInstance = 'App\Models\\' . $modifyModel;
    
            $dedModelInstance::where('pay_id', $id)->delete();
            $modifyModelInstance::where('pay_id', $id)->delete();
    
            PayrollFile::where('payroll_ID', $id)->delete();
        }
    
        Code::where('payroll_id', $id)->delete();
        $payroll->delete();
    
        return response()->json([
            'status' => 200,
            'message' => 'Deleted Successfully',
        ]);
    }
    
    

    public function deletePayrollFiles($id){
        $PayrollFile = PayrollFile::find($id);
    
        $statID = $PayrollFile->stat_ID;
    
        if (!$PayrollFile) {
            return response()->json([
                'status' => 404,
                'message' => 'PayrollFile not found',
            ]);
        }
    
        if ($statID == 1) {
            $dedModel = 'Deduction';
            $modifyModel = 'Modify';
        }elseif ($statID == 3){
            $dedModel = 'DeductionPartimeJo';
            $modifyModel = 'ModifyPartimeJo';
        }elseif ($statID == 4) {
            $dedModel = 'DeductionJo';
            $modifyModel = 'ModifyJo';
        }
    
        $dedModelInstance = 'App\Models\\' . $dedModel;
        $modifyModelInstance = 'App\Models\\' . $modifyModel;
    
        $id1 = $PayrollFile->id;
        $dedModelInstance::where('payroll_id', $id1)->delete();
        $modifyModelInstance::where('payroll_id', $id1)->delete();
        $PayrollFile->delete();
    
        return response()->json([
            'status' => 200,
            'message' => 'Deleted Successfully',
        ]);
    }
    
    public function storepayroll($payrollID, $statID, $offID){
        $payroll = Payroll::find($payrollID);
        $office = Office::all()->where('office_name', '!=', 'UNKNOWN');
        $PayrollFile = PayrollFile::where('payroll_ID', $payroll->id)->first();    
        $codes = Code::where('payroll_id', $payrollID)->get();
        
        if ($statID == 1) {
            $modeldeduct = 'Deduction';
            $dedtable = 'deductions';

            $modelmodify = 'modify';
            $modtable = 'modifies';
        }elseif ($statID == 3) {
            $modeldeduct = 'DeductionPartimeJo';
            $dedtable = 'deduction_partime_jos';

            $modelmodify = 'ModifyPartimeJo';
            $modtable = 'modify_partime_jos';
        } 
        elseif ($statID == 4) {
            $modeldeduct = 'DeductionJo';
            $dedtable = 'deduction_jos';

            $modelmodify = 'ModifyJo';
            $modtable = 'modify_jos';
        }

        $modelInstance = 'App\Models\\' . $modeldeduct;
        $modelInstance1 = 'App\Models\\' . $modelmodify;
        
        if($offID == "All"){
            $finalCond = 'o.id != ' . 0; 
        }
        else{
            $office1 = Office::where('id', $offID)->first();
            $offgroup = $office1->group_by;
            $finalCond = $offgroup == 0 ? 'o.id = ' . $offID : 'o.group_by = ' . $offgroup;
        }

        $deduction = $modelInstance::join('payroll_files as pf', $dedtable.'.payroll_id', '=', 'pf.id')
        ->join('employees as emp', 'pf.emp_id', '=', 'emp.id')
        ->join('offices as o', 'emp.emp_dept', '=', 'o.id')
        ->where('pf.payroll_ID', $payrollID)
        ->where('pf.status', '!=', '3')
        ->where('pf.status', 1)
        ->whereRaw($finalCond)
        ->get();
        
        if($PayrollFile){
            $modify1 = $modelInstance1::where('pay_id', $payroll->id)
            ->join('offices as of', $modtable.'.off_id', '=', 'of.id')
            ->get();

            $modifyRef = $modelInstance1::where('pay_id', $payrollID)
            ->join('offices AS o', $modtable.'.off_id', '=', 'o.id')
            ->where('action', 'Refund')
            ->whereRaw($finalCond)
            ->get();
        
            $modifyRef = $modifyRef->groupBy('column')
            ->map(function ($group) {
                return $group->sum('amount');
            });

            $modifyDed = $modelInstance1::where('pay_id', $payrollID)
            ->join('offices AS o', $modtable.'.off_id', '=', 'o.id')
            ->where('action', 'Deduction')
            ->whereRaw($finalCond)
            ->get();

            $modifyDed = $modifyDed->groupBy('column')
            ->map(function ($group) {
                return $group->sum('amount');
            });

        }else{
            $modify1 = null;
            $modifyRef = null;
            $modifyDed = null;
        }

        $campId = $payroll->campus_id;
        $startDate = $payroll->payroll_dateStart;
        $endDate = $payroll->payroll_dateEnd;
        $days = $payroll->number_days;
        
        if($statID == 3){
            $employee = Employee::all()->where('camp_id', $campId)->where('partime_rate', '!=', 0);
        }
        else{
            $employee = Employee::all()->where('emp_status', $statID);
        }

        try {
            $pfiles = PayrollFile::query()
            ->join($dedtable.' AS d', 'payroll_files.id', '=', 'd.payroll_id')
            ->join('employees AS e', 'payroll_files.emp_id', '=', 'e.id')
            ->join('offices AS o', 'e.emp_dept', '=', 'o.id')
            ->leftJoinSub(function ($query) {
                $query->from('modifies')
                    ->select('payroll_id', 
                        DB::raw('SUM(CASE WHEN action = "Refund" THEN amount ELSE 0 END) as sumRef'),
                        DB::raw('SUM(CASE WHEN action = "Deduction" THEN amount ELSE 0 END) as sumDed')
                    )
                    ->groupBy('payroll_id');
            }, 'm', 'payroll_files.id', '=', 'm.payroll_id')
            ->select('payroll_files.id as pid', 'payroll_files.*', 'e.*', 'd.*', 'o.office_abbr', 'm.sumRef', 'm.sumDed')
            ->where('payroll_files.payroll_ID', '=', $payrollID)
            ->where('payroll_files.camp_ID', '=', $campId)
            ->where('payroll_files.stat_ID', '=', $statID)
            ->where('payroll_files.startDate', '=', $startDate)
            ->where('payroll_files.endDate', '=', $endDate)
            // ->where(function($query) {
            //     $query->where('o.id', '=', 64)
            //     ->orWhere('o.id', '=', 65);
            // })
            ->whereRaw($finalCond)
            ->get();
            
            $empStat = Status::find($statID);
            $empStat = $empStat->status_name;
            $currentcamp = DB::select("SELECT * FROM campuses WHERE id ='$campId' ");

            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);

            $startFormatted = $start->format('F j');
            $endFormatted = $end->format('j, Y');

            if ($start->format('F Y') === $end->format('F Y')) {
                $daterange = $startFormatted . '-' . $endFormatted;
            } else {
                $daterange = $startFormatted . '-' . $endFormatted;
            }

            $middle = (clone $start)->setDate($start->format('Y'), $start->format('m'), 15);

            $month = $start->format('F');
            $firstHalf = $month.' '.$start->format('j') .'-'. $middle->format('j, Y');
            $secondHalf = $middle->modify('+1 day')->format('F j') .'-'. $end->format('j, Y');

            $page = ($statID == 1) ? "storepayroll" : ($statID == 3 ? "storepayroll_partime_jo" : ($statID == 4 ? "storepayroll_jo" : ''));

            if(auth()->user()->role == "Administrator" || auth()->user()->role == "Payroll Administrator"){
                $status = Status::all();
                $camp = Campus::all();
            }
            else{
                if(auth()->user()->campus_id != $campId){
                    return redirect()->route('dashboard')->with('error1', 'You do not have permission to access this page');
                }    
                $status = Status::where('status_name', '!=', 'Regular')->get();
                $camp = Campus::find(auth()->user()->campus_id)->get();
            }

            if($PayrollFile){
                return view('payroll.'.$page, compact('camp', 'office', 'offID', 'status', 'currentcamp', 'empStat', 'pfiles', 'campId', 'statID', 'payrollID', 'employee', 'codes', 'days', 'firstHalf', 'secondHalf', 'daterange', 'deduction', 'modify1', 'modifyRef', 'modifyDed'));
            }
            else{
                return view('payroll.'.$page, compact('camp', 'office', 'offID', 'status', 'currentcamp', 'empStat', 'pfiles', 'campId', 'statID', 'payrollID', 'employee', 'codes', 'days', 'firstHalf', 'secondHalf', 'daterange', 'modify1', 'modifyRef', 'modifyDed'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . 'You do not have permission to access this page'); 
        }
    }

    public function updateCode(Request $request) {
        $id = $request->input('id');
        $code = Code::find($id);
        $status = $code->status;
        if($status == 'checked'){
            $status = '';
        }
        else{
            $status = $request->input('status');
        }

        if($request->type =="checkbox"){
            $update = [
                'status'=>$status,
            ];

            DB::table('codes')->where('id', $id)->update($update);
        
            return response()->json([
                'status' => 200,
                'message' => "Updated Successfully",
            ]);
        }

        if($request->type =="code"){
            $code = $request->input('code');
            if($code == ""){
                $code = "";
            }
            else{
                $code = $request->input('code');
            }
            $update = [
                'id'=>$request->input('id'),
                'code'=>$code,
            ];

            DB::table('codes')->where('id', $request->id)->update($update);
        
            return response()->json([
                'status' => 200,
                'message' => "Updated Successfully",
            ]);
        }
    }

    public function pdfRemittance($col, $payrollID){
        ini_set('memory_limit', '1024M');
        $deduct = "deductions";

        $payroll = Payroll::find($payrollID);
        $dateStart = date('F Y', strtotime($payroll->payroll_dateStart));

        $datas = Deduction::join('employees', 'deductions.emp_id', '=', 'employees.id')
        ->select('employees.*', 'deductions.' . $col)
        ->where('deductions.pay_id', $payrollID)
        ->orderBy('employees.lname')
        ->get();
    
    
        $datas = $datas->all(); 

        $customPaper = array(0, 0, 792, 842);

        $viewTemplate = 'payroll.pdf_remittance';
        $pdf = \PDF::loadView($viewTemplate, compact('datas', 'col', 'dateStart'))->setPaper($customPaper);

        $pdf->setCallbacks([
            'before_render' => function ($domPdf) {
                $domPdf->getCanvas()->page_text(10, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            },
        ]);

        $pdf->render();

        return $pdf->stream();
    }

    public function showPdf($payrollID, $statID, $pid, $offid)
    {
        $currRoute = Route::currentRouteName();

           if ($statID == 1) {
            $modeldeduct = 'Deduction';
            $dedtable = 'deductions';
            
            $modelmodify = 'modify';
            $modtable = 'modifies';
        }elseif ($statID == 3) {
            $modeldeduct = 'DeductionPartimeJo';
            $dedtable = 'deduction_partime_jos';

            $modelmodify = 'ModifyPartimeJo';
            $modtable = 'modify_partime_jos';
        } 
        elseif ($statID == 4) {
            $modeldeduct = 'DeductionJo';
            $dedtable = 'deduction_jos';

            $modelmodify = 'ModifyJo';
            $modtable = 'modify_jos';
        }

        $modelInstance = 'App\Models\\' . $modeldeduct;
        $modelInstance1 = 'App\Models\\' . $modelmodify;

        ini_set('memory_limit', '3072M');

        $office = Office::where('id', $offid)->first();
        
        $offid == 'All' ? $offgroup = 0 : $offgroup = $office->group_by;
        $offgroup == 'All' ? $finalCond = 'of.group_by != ' . $offgroup. 'ORDER BY group_by': $finalCond = 'of.group_by = ' . $offgroup;

        $payroll = Payroll::find($payrollID);

        $modify = $modelInstance1::where('pay_id', $payroll->id)
        ->join('offices as of', $modtable.'.off_id', '=', 'of.id')
        ->whereRaw($finalCond)
        ->get();

        $modify1 = $modelInstance1::where('pay_id', $payroll->id)
        ->join('offices as of', $modtable.'.off_id', '=', 'of.id')
        ->get();

        $stat = request('stat');   

        $campID = $payroll->campus_id;
        $dateStart = $payroll->payroll_dateStart;
        $dateEnd = $payroll->payroll_dateEnd;

        $dateStartM = date('F Y', strtotime($payroll->payroll_dateStart));
        
        if ($statID == 1 || $statID == 4) {
            $start = new \DateTime($dateStart);
            $end = new \DateTime($dateEnd);

            $startFormatted = $start->format('F j');
            $endFormatted = $end->format('F j, Y');
            $fulldate = "$startFormatted-" . $end->format('j, Y');

            $middle = (clone $start)->setDate($start->format('Y'), $start->format('m'), 15);

            $month = $start->format('F');
            $firstHalf = $month.' '.$start->format('j') .'-'. $middle->format('j, Y');
            $secondHalf = $middle->modify('+1 day')->format('F j') .'-'. $end->format('j, Y');
        }
        
        $statuses = Status::find($statID);
        $office = Office::all();
        $code = Code::where('payroll_id', $payrollID)->get();
        
        $datas = DB::table('payroll_files')
            ->join($dedtable, 'payroll_files.id', '=', $dedtable . '.payroll_id')
            ->join('employees', 'payroll_files.emp_id', '=', 'employees.id')
            ->join('offices as of', 'employees.emp_dept', '=', 'of.id')
            ->leftJoinSub(function ($query) {
                $query->from('modifies')
                    ->select('payroll_id', 
                        DB::raw('SUM(CASE WHEN action = "Refund" THEN amount ELSE 0 END) as sumRef'),
                        DB::raw('SUM(CASE WHEN action = "Deduction" THEN amount ELSE 0 END) as sumDed')
                    )
                    ->groupBy('payroll_id');
            }, 'm', 'payroll_files.id', '=', 'm.payroll_id')
            ->select('of.id as offid', 'payroll_files.id as pid', 'of.*', 'payroll_files.*', 'employees.*', $dedtable . '.*', 'm.sumRef', 'm.sumDed')
            ->where('payroll_files.payroll_ID', $payrollID)
            ->where('payroll_files.camp_ID', $campID)
            ->where('payroll_files.stat_ID', $statID)
            ->where('payroll_files.startDate', $dateStart)
            ->where('payroll_files.endDate', $dateEnd)
            ->where('payroll_files.endDate', $dateEnd)
            ->where('payroll_files.status', $stat)
            // ->where(function($query) {
            //     $query->where('of.id', '=', 64)
            //           ->orWhere('of.id', '=', 65);
            // })
            ->orderBy('employees.lname')
            ->get();

            // if ($datas->isEmpty()) {
            //     return back()->with('error', 'No data found.');
            // }
        
            $datas = $datas->all(); 
            $customPaper = array(0, 0, 550, 1008);
            if($statID == 1){
              
                $currRoute == "transmittal" ? $viewTemplate = 'payroll.pdf_transmittal' : $viewTemplate = $pid == 1 ? 'payroll.pdf_payrollform_reg' : 'payroll.pdf_payrollform_reg2' ;
              
                $pdf = \PDF::loadView($viewTemplate, compact('datas', 'finalCond', 'fulldate', 'firstHalf', 'secondHalf', 'code', 'modify', 'modify1', 'pid', 'offid', 'office', 'dateStartM'))->setPaper($customPaper, 'landscape');
            
            }
            if($statID == 4){
                $viewTemplate = 'payroll.pdf_payrollform_jo';
                $pdf = \PDF::loadView($viewTemplate, compact('datas', 'firstHalf', 'secondHalf', 'code', 'modify1', 'pid', 'offid'))->setPaper($customPaper, 'landscape');
            }

            $pdf->setCallbacks([
                'before_render' => function ($domPdf) {
                    $domPdf->getCanvas()->page_text(10, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                },
            ]);

            $pdf->render();

            return $pdf->stream();
    }

    public function payslip($payrollID){
  
        $payslip = PayrollFile::join('payrolls', 'payroll_files.payroll_ID', '=', 'payrolls.id')
        ->join('employees', 'payroll_files.emp_id', '=', 'employees.id')
        ->select('payroll_files.*', 'employees.*', 'employees.id as empid')
        ->where('payroll_files.payroll_ID', $payrollID)
        ->get();
        
        $camp = Campus::all();
    
        if (auth()->user()->role == "Administrator" || auth()->user()->role == "Payroll Administrator") {
            return view("payroll.payslip", compact('payslip', 'camp', 'payrollID'));
        } else {
            return redirect()->route('dashboard')->with('error1', 'You do not have permission to access this page');
        }
    
    }

    public function payslipGen(Request $request)
    {
        $payslipsPerPage = 4;
        
        $payslip = PayrollFile::join('payrolls', 'payroll_files.payroll_ID', '=', 'payrolls.id')
        ->join('employees', 'payroll_files.emp_id', '=', 'employees.id')
        ->join('deductions', 'employees.id', '=', 'deductions.emp_id')
        ->select('payroll_files.*', 'payroll_files.id as pfile_id', 'employees.*', 'deductions.*')
        ->where('payroll_files.payroll_ID', $request->payrol_ID)
        ->whereIn('employees.id', $request->emp_ID)
        ->orderBy('employees.emp_dept')
        ->get();

        $middleIndex = floor($payslip->count() / 2);

        $payslip1 = $payslip->slice(0, $middleIndex);
        $payslip2 = $payslip->slice($middleIndex);

        $modify = Modify::all();
    
        $totalPayslips = $payslip->count();
        $totalPages = ceil($totalPayslips / $payslipsPerPage);
    
        $legalPaperLandscape = [0, 0, 612, 936]; // 8.5 x 13 inches in landscape (936 = 8.5 * 72)
        $pdf = PDF::loadView('payroll.payslip_generate', compact('payslip', 'payslip1', 'modify', 'totalPages'))->setPaper($legalPaperLandscape, 'portrait');
              
        
        $pdf->setOptions(['enable_php' => true]);
    
        $pdf->setCallbacks([
            'before_render' => function ($domPdf) {
                $domPdf->getCanvas()->page_text(10, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);
            },
        ]);
    
        return $pdf->stream();
    }

    public function saltypepUp($id, $val){
        $pfile = [
            'sal_type' => $val
        ];

        PayrollFile::where('id', $id)->update($pfile);

        return redirect()->back()->with('success', 'Updated Successfully');
    }

    public function statUpdate($id, $val){
        $pfile = [
            'status' => $val
        ];

        PayrollFile::where('id', $id)->update($pfile);

        return redirect()->back()->with('success', 'Updated Successfully');
    }

}
