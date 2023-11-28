<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\Deductiontwo;
use App\Models\Code;
use App\Models\Modify;
use PDF;

class PayrollController extends Controller
{
    
    public function viewPayroll($campId)
    {
        try {
            $campId = Crypt::decrypt($campId);
            $pays = Payroll::join('campuses', 'payrolls.campus_id', '=', 'campuses.id')
                ->join('statuses', 'payrolls.payroll_id', '=', 'statuses.id')
                ->select('payrolls.*', 'campuses.campus_name', 'statuses.status_name')
                ->where('payrolls.campus_id', $campId)
                ->get();
    
            if (auth()->user()->role == "Administrator" || auth()->user()->role == "Payroll Administrator") {
                $status = Status::all();
                $camp = Campus::all();
            } elseif (auth()->user()->role == "Payroll Extension" && auth()->user()->campus_id != $campId) {
                throw new \Exception('You do not have permission to access this page');
            } else {
                $status = Status::where('status_name', '!=', 'Regular')->get();
                $camp = Campus::find(auth()->user()->campus_id)->get();
            }

            return view("payroll.viewPayroll", compact('camp', 'status', 'campId', 'pays'));

        } catch (\Exception $e) { 
            return redirect()->back()->with('error', 'You do not have permission to access this page');  
        }
    
    } 

    public function createPayroll(Request $request){
        $campID = $request->input('campID');
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
                'payroll_id'=>$request->input('statName'),
                'campus_id'=>$request->input('campID'),
                'payroll_dateStart'=>$request->input('PayrollDateStart'),
                'payroll_dateEnd'=>$request->input('PayrollDateEnd'),
                'number_days'=>$request->input('number_days')
            ]);


            if ($request->input('statName') == 1) {
                $codes = [
                    ['code_for' => 'Regular', 'code_name' => 'GSIS Payable', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Due to PAG-IBIG(MPL)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Due to PAG-IBIG(Premium)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Due to PhilHealth', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Due to BIR', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(CSB)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(CFI)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(LBP)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(NSCA MPC)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(Projects)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(Grad School)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(FASFED)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Other Payable(Absences)', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Due to Officers & Employee', 'code' => '', 'status' => ''],
                    ['code_for' => 'Regular', 'code_name' => 'Due to Officers & Employee', 'code' => '', 'status' => ''],
                ];
                
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    DB::table('codes')->insert($code);
                }
            }

            elseif ($request->input('statName') == 2) {
                $codes = [
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time', 'code_name' => 'Due to Bir', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                ];
            
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    DB::table('codes')->insert($code);
                }
            }            
            

            if ($request->input('statName') == 3){
                $codes = [
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                    ['code_for' => 'Part-time/JO', 'code_name' => 'Other Payables', 'code' => '', 'status' => ''],
                ];
            
                foreach ($codes as $code) {
                    $code['payroll_id'] = $payrollID;
                    DB::table('codes')->insert($code);
                }
            }
            

            if ($request->input('statName') == 4) {
                $codes = [
                    ['code_for' => 'Job Order', 'code_name' => 'Labor and Wages', 'code' => '50216010 01'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable (NSCA Coop)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Due to BIR (1%)', 'code' => '20201010 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Due to BIR (2%)', 'code' => '20201010 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Receivable', 'code' => '10305990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable(NSCA MPC)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable(Grad Sch.)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Other Payable(Project)', 'code' => '29999990 00'],
                    ['code_for' => 'Job Order', 'code_name' => 'Cash in Bank-LC, LBP', 'code' => '10102020 24'],
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
        Code::where('payroll_id', $id)->delete();

        if (!$payroll) {
            return response()->json([
                'status' => 404,
                'message' => 'Payroll not found',
            ]);
        }
    
        $PayrollFile = PayrollFile::where('payroll_ID', $id)->first();

        if ($PayrollFile) {
            $id1 = $PayrollFile->id;
        
            Deduction::where('payroll_id', $id1)->delete();
            Modify::where('pay_id', $id)->delete();
        
            $PayrollFile->delete();
        }
        
    
        $payroll->delete();
    
        return response()->json([
            'status' => 200,
            'message' => 'Deleted Successfully',
        ]);
    }

    public function deletePayrollFiles($id){
        $PayrollFile = PayrollFile::find($id);

        if ($PayrollFile) {
            $id1 = $PayrollFile->id;
            Deduction::where('payroll_id', $id1)->delete();
            Modify::where('payroll_id', $id1)->delete();
            $PayrollFile->delete();
        }
        return response()->json([
            'status'=>200,
            'message'=>"Deleted Successfully",
        ]);
    }
    
    public function storepayroll($payrollID, $statID, $offID){
        $payroll = Payroll::find($payrollID);
        $office = Office::all()->where('office_name', '!=', 'UNKNOWN');
        $PayrollFile = PayrollFile::where('payroll_ID', $payroll->id)->first();    
        $codes = Code::where('payroll_id', $payrollID)->get();
        
        if($offID == "All"){
            $finalCond = 'o.id != ' . 0; 
        }
        else{
            $office1 = Office::where('id', $offID)->first();
            $offgroup = $office1->group_by;
            $finalCond = $offgroup == 0 ? 'o.id = ' . $offID : 'o.group_by = ' . $offgroup;
        }

        $deduction = Deduction::join('payroll_files as pf', 'deductions.payroll_id', '=', 'pf.id')
        ->join('employees as emp', 'pf.emp_id', '=', 'emp.emp_ID')
        ->join('offices as o', 'emp.emp_dept', '=', 'o.id')
        ->where('pf.payroll_ID', $payrollID)
        ->whereRaw($finalCond)
        ->get();

        if($PayrollFile){
            $modify1 = Modify::where('pay_id', $payroll->id)
            ->join('offices as of', 'modifies.off_id', '=', 'of.id')
            ->get();

            $modifyRef = Modify::where('pay_id', $payrollID)
            ->join('offices AS o', 'modifies.off_id', '=', 'o.id')
            ->where('action', 'Refund')
            ->whereRaw($finalCond)
            ->get();
        
            $modifyRef = $modifyRef->groupBy('column')
            ->map(function ($group) {
                return $group->sum('amount');
            });

            $modifyDed = Modify::where('pay_id', $payrollID)
            ->join('offices AS o', 'modifies.off_id', '=', 'o.id')
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
            $employee = Employee::all()->where('emp_status', $statID)->where('camp_id', $campId)->where('partime_rate', '!=', 0);
        }
        else{
            $employee = Employee::all()->where('emp_status', $statID)->where('camp_id', $campId);
        }

        // try {
            $pfiles = DB::table('payroll_files AS pf')
            ->join('deductions AS d', 'pf.id', '=', 'd.payroll_id')
            ->join('employees AS e', 'pf.emp_id', '=', 'e.emp_ID')
            ->join('offices AS o', 'e.emp_dept', '=', 'o.id')
            ->leftJoinSub(function ($query) {
                $query->from('modifies')
                    ->select('payroll_id', 
                        DB::raw('SUM(CASE WHEN action = "Refund" THEN amount ELSE 0 END) as sumRef'),
                        DB::raw('SUM(CASE WHEN action = "Deduction" THEN amount ELSE 0 END) as sumDed')
                    )
                    ->groupBy('payroll_id');
            }, 'm', 'pf.id', '=', 'm.payroll_id')
            ->select('pf.id as pid', 'pf.*', 'e.*', 'd.*', 'o.*', 'm.sumRef', 'm.sumDed')
            ->where('pf.payroll_ID', '=', $payrollID)
            ->where('pf.camp_ID', '=', $campId)
            ->where('pf.stat_ID', '=', $statID)
            ->where('pf.startDate', '=', $startDate)
            ->where('pf.endDate', '=', $endDate)
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
                $daterange = $startFormatted . ' - ' . $endFormatted;
            }

            $middle = (clone $start)->setDate($start->format('Y'), $start->format('m'), 15);

            $month = $start->format('F');
            $firstHalf = $month.' '.$start->format('j') .'-'. $middle->format('j, Y');
            $secondHalf = $middle->modify('+1 day')->format('F j') .'-'. $end->format('j, Y');

            $statID == 1 ? $page = "storepayroll" : $page = "storepayroll_jo";
                 
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
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'An error occurred: ' . 'You do not have permission to access this page'); 
        // }
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

    public function showPdf($payrollID, $statID, $pid, $offid)
    {
        $deduct = "deductions";
        if($offid == "All" && $statID == 1){
            return redirect()->back()->with('error', 'Select Office / Department first');
        }
        $offCond = $offid !== 'All' ? '=' :  '!=';
        $office = Office::where('id', $offid)->first();
        
        if($statID == 1){
            $offgroup = $office->group_by;
            $finalCond = $offgroup == 0 ? 'of.id = ' . $offid : 'of.group_by = ' . $offgroup;
        }
        if($statID != 1){
            $finalCond = 'of.id != 0';
        }
        $payroll = Payroll::find($payrollID);
        $modify = Modify::where('pay_id', $payroll->id)
        ->join('offices as of', 'modifies.off_id', '=', 'of.id')
        ->whereRaw($finalCond)
        ->get();

        $modify1 = Modify::where('pay_id', $payroll->id)
        ->join('offices as of', 'modifies.off_id', '=', 'of.id')
        ->get();

        $modifyref = Modify::where('pay_id', $payroll->id)
        ->join('offices as of', 'modifies.off_id', '=', 'of.id')
        ->where('action', 'Refund')
        ->whereRaw($finalCond)
        ->get();

        $totalref = 0;
        $totalref += $modifyref->sum('amount');
        
        $modifyref1 = Modify::where('pay_id', $payroll->id)
        ->join('offices as of', 'modifies.off_id', '=', 'of.id')
        ->where('action', 'Refund')
        ->get();

        $totalref1 = 0;
        $totalref1 += $modifyref1->sum('amount');

        $modifyded = Modify::where('pay_id', $payroll->id)
        ->join('offices as of', 'modifies.off_id', '=', 'of.id')
        ->where('action', 'Deduction')
        ->whereRaw($finalCond)
        ->get();

        $totalded = 0;
        $totalded += $modifyded->sum('amount');

        $modifyded1 = Modify::where('pay_id', $payroll->id)
        ->join('offices as of', 'modifies.off_id', '=', 'of.id')
        ->where('action', 'Deduction')
        ->get();

        $totalded1 = 0;
        $totalded1 += $modifyded1->sum('amount');

        // JO

        $campID = $payroll->campus_id;
        $dateStart = $payroll->payroll_dateStart;
        $dateEnd = $payroll->payroll_dateEnd;
        
        if ($statID == 1 || $statID == 4) {
            $start = new \DateTime($dateStart);
            $end = new \DateTime($dateEnd);

            $middle = (clone $start)->setDate($start->format('Y'), $start->format('m'), 15);

            $month = $start->format('F');
            $firstHalf = $month.' '.$start->format('j') .'-'. $middle->format('j, Y');
            $secondHalf = $middle->modify('+1 day')->format('F j') .'-'. $end->format('j, Y');
        }
        
        $statuses = Status::find($statID);
        $code = Code::where('payroll_id', $payrollID)->get();
        
        $datas = DB::table('payroll_files')
            ->join($deduct, 'payroll_files.id', '=', $deduct . '.payroll_id')
            ->join('employees', 'payroll_files.emp_id', '=', 'employees.emp_ID')
            ->join('offices as of', 'employees.emp_dept', '=', 'of.id')
            ->leftJoinSub(function ($query) {
                $query->from('modifies')
                    ->select('payroll_id', 
                        DB::raw('SUM(CASE WHEN action = "Refund" THEN amount ELSE 0 END) as sumRef'),
                        DB::raw('SUM(CASE WHEN action = "Deduction" THEN amount ELSE 0 END) as sumDed')
                    )
                    ->groupBy('payroll_id');
            }, 'm', 'payroll_files.id', '=', 'm.payroll_id')
            ->select('payroll_files.id as pid', 'payroll_files.*', 'employees.*', $deduct . '.*', 'm.sumRef', 'm.sumDed')
            ->where('payroll_files.payroll_ID', $payrollID)
            ->where('payroll_files.camp_ID', $campID)
            ->where('payroll_files.stat_ID', $statID)
            ->where('payroll_files.startDate', $dateStart)
            ->where('payroll_files.endDate', $dateEnd)
            ->whereRaw($finalCond)
            ->get();
        
        $chunkedDatas = $datas->chunk(15); // Split the data into chunks of 15 rows

        $datas1 = DB::table('payroll_files')
        ->join($deduct, 'payroll_files.id', '=', $deduct . '.payroll_id')
        ->join('employees', 'payroll_files.emp_id', '=', 'employees.emp_ID')
        ->join('offices as of', 'employees.emp_dept', '=', 'of.id')
        ->leftJoinSub(function ($query) {
            $query->from('modifies')
                ->select('payroll_id', 
                    DB::raw('SUM(CASE WHEN action = "Refund" THEN amount ELSE 0 END) as sumRef'),
                    DB::raw('SUM(CASE WHEN action = "Deduction" THEN amount ELSE 0 END) as sumDed')
                )
                ->groupBy('payroll_id');
        }, 'm', 'payroll_files.id', '=', 'm.payroll_id')
        ->select('payroll_files.id as pid', 'payroll_files.*', 'employees.*', $deduct . '.*', 'm.sumRef', 'm.sumDed')
        ->where('payroll_files.payroll_ID', $payrollID)
        ->where('payroll_files.camp_ID', $campID)
        ->where('payroll_files.stat_ID', $statID)
        ->where('payroll_files.startDate', $dateStart)
        ->where('payroll_files.endDate', $dateEnd)
        ->get();
    
        $chunkedDatas1 = $datas1->chunk(15); // Split the data into chunks of 15 rows

        // $customPaper = array(0, 0, 612, 1008);
        $customPaper = array(0, 0, 550, 1008);
        // Use different view templates for different statuses
        if($statID == 1){
            $viewTemplate = 'payroll.pdf_payrollform1';
            $pdf = \PDF::loadView($viewTemplate, compact('chunkedDatas', 'chunkedDatas1', 'firstHalf', 'secondHalf', 'code', 'modify', 'modify1', 'modifyref', 'modifyref1','modifyded', 'modifyded1','totalref', 'totalref1','totalded', 'totalded1','pid', 'offid'))->setPaper($customPaper, 'landscape');
        }
        if($statID == 4){
            $viewTemplate = 'payroll.pdf_payrollform_jo';
            $pdf = \PDF::loadView($viewTemplate, compact('chunkedDatas', 'chunkedDatas1', 'firstHalf', 'secondHalf', 'code', 'modify1', 'pid', 'offid'))->setPaper($customPaper, 'landscape');
        }
        
        $pdf->setCallbacks([
            'before_render' => function ($domPdf) {
                $domPdf->getCanvas()->page_text(10, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            },
        ]);

        $pdf->render(); // Generate the PDF

        return $pdf->stream();
    }

    public function payslip($payrollID){
  
        $payslip = PayrollFile::join('payrolls', 'payroll_files.payroll_ID', '=', 'payrolls.id')
        ->join('employees', 'payroll_files.emp_id', '=', 'employees.emp_ID')
        ->select('payroll_files.*', 'employees.*')
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
        $employee_ids = $request->emp_ID;
        if (!is_array($employee_ids)) {
            $employee_ids = [$employee_ids]; // Convert string to array
        }

        $payslipsPerPage = 4; // Number of payslips per page

        $payslip = PayrollFile::join('payrolls', 'payroll_files.payroll_ID', '=', 'payrolls.id')
            ->join('employees', 'payroll_files.emp_id', '=', 'employees.emp_ID')
            ->select('payroll_files.*', 'employees.*')
            ->where('payroll_files.payroll_ID', $request->payrol_ID)
            ->get();

        $totalPayslips = $payslip->count();
        $totalPages = ceil($totalPayslips / $payslipsPerPage); // Calculate total number of pages

        // Get the size of payslip_generate page
        $pageWidth = 792; // Update with the desired width in points (e.g., 792 for 8.5" in landscape)
        $pageHeight = 1224; // Update with the desired height in points (e.g., 1224 for 14" in landscape)

        $customPaper = array(0, 0, $pageWidth, $pageHeight); // Payslip page dimensions in points
        $pdf = \PDF::loadView('payroll.payslip_generate', compact('payslip', 'totalPages'))->setPaper($customPaper, 'landscape');

        $pdf->setOptions(['enable_php' => true]); // Enable inline PHP code in the view

        $pdf->setCallbacks([
            'before_render' => function ($domPdf) {
                $domPdf->getCanvas()->page_text(10, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            },
        ]);

        $pdf->render(); // Generate the PDF

        //return $pdf->stream();
        return view('payroll.payslip_generate', compact('payslip', 'totalPages'));
    }

    public function saltypepUp($id, $val){
        $pfile = [
            'sal_type' => $val
        ];

        PayrollFile::where('id', $id)->update($pfile);

        return redirect()->back()->with('success', 'Updated Successfully');
    }

}
