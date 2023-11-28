@extends('layouts.master')

@section('body')
<style>
.styled-table {
border-collapse: collapse;
width: 100%; 
margin: 2px; 
}

.styled-table thead {
background-color: #f2f2f2; 
}

.styled-table th {
padding: 5px;
text-align: left;
border: 1px solid #ddd; 
}

.styled-table tr {
background-color: #ffffff; 
}

.styled-table td {
padding: 5px;
text-align: left;
border: 1px solid #ddd; 
}

th{
    font-size: 12px;
}
</style>
<div class="container-fluid">
    <div class="row" style="padding-top: 100px;">
        <div class="col-lg-12">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="card-title">
                                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-importPayrollsTwo">
                                    <i class="fas fa-plus"></i> Add New
                                </button>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-codes">
                                    <i class="fas fa-plus"></i> Code
                                </button>
                                <div class="btn-group">
                                    @php
                                        $firstHalfformated = preg_replace('/(January|February|March|April|May|June|July|August|September|October|November|December)/', '', $daterange);
                                        $dateParts = explode('-', $firstHalfformated);
                                        $day = (int)$dateParts[0];
                                        $pid = 1;
                                        if($day >= 16){
                                            $pid = 2;
                                        }
                                    @endphp
                                    <a href="{{ route("pdf", ['payrollID' => $payrollID, 'statID' => $statID, 'pid' => $pid, 'offid' => $offID, 'stat' => 1]) }}" target="_blank">
                                        <button id="open-pdf" target="_blank"  class="btn btn-info btn-sm">
                                            <i class="fas fa-print"></i> Print Payroll
                                        </button>
                                    </a>
                                </div>
                            </h3>
                        </div>
                        <div class="col-md-8">
                            <ol class="breadcrumb float-md-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item">Payroll</li>
                                @foreach($currentcamp as $c)
                                    @php
                                        $encryptedId = encrypt($c->id);
                                    @endphp
                                    <li class="breadcrumb-item"><a href="{{ route('viewPayroll', $encryptedId) }}">{{ $c->campus_abbr }}</a></li>
                                @endforeach
                                <li class="breadcrumb-item active">{{ $empStat }} Payroll - {{ $daterange }}</li>
                            </ol>                            
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">

                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <select class="form-control select2" name="offid" onchange="navigateToPage(this.value)" required>
                                    <option value="All">All</option>
                                    @foreach($office as $off)
                                        <option value="{{ $off->id }}" @if($off->id == $offID) selected @endif>{{ $off->office_name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                </div>
                            </div>      
                        </div>                                        
                        
                        @php
                        $modifyth = array_fill_keys(['Column1', 'Column2', 'Column3', 'Column4', 'Column5'], 0);
                        $totaljoAdd = 0; 
                        @endphp
                        
                        @php
                        $columns_jo = ['Column1' => 0, 'Column2' => 0, 'Column3' => 0, 'Column4' => 0, 'Column5' => 0];
                        @endphp
                        
                        
                        @if(isset($modify1))
                        @foreach ($modify1 as $mody)
                            @if ($mody->pay_id == $payrollID && $mody->action == 'Additionals' && array_key_exists($mody->column, $columns_jo))
                                @php
                                    $columns_jo[$mody->column] += $mody->amount;
                                @endphp
                            @endif
                        @endforeach

                            @foreach ($modify1 as $mody)
                            @if ($mody->action == 'Additionals' && array_key_exists($mody->column, $modifyth))
                                @php
                                    $modifyth[$mody->column] += $mody->amount;
                                    $modifyth[$mody->column] = ($modifyth[$mody->column] >= 1) ? 1 : 0;
                                @endphp
                            @endif
                            @endforeach
                        @endif
                        
                        <div class="col-12">
                            <div class="table-responsive" style="overflow-y: auto;
                                overflow-x: auto; ">
                                <br>
                                <table id="example1" class="table table-bordered table-hover table-pay">
                                    <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th width="6%">Emp. ID</th>
                                                <th width="12%">Full Name</th>
                                                <th>Designation</th>
                                                <th>No. Hours/Month</th>
                                                <th>Rate per hr.</th>
                                                @if(isset($modify1))
                                                    @foreach ($modify1 as $mody)
                                                        @if ($mody->action === 'Additionals' && array_key_exists($mody->column, $columns_jo))
                                                            @php
                                                                $columns_jo[$mody->column] += $mody->amount;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @foreach ($columns_jo as $column => $total)
                                                    @if ($total != 0.00)
                                                    @foreach ($modify1 as $mody)
                                                        @if ($mody->column === $column)
                                                            @php
                                                                $label = preg_replace('/(January|February|March|April|May|June|July|August|September|October|November|December)/', '$1<br>', $mody->label);
                                                            @endphp
                                                            <th style="text-align: center">{!! $label !!}</th>
                                                            @break
                                                        @endif
                                                    @endforeach
                                                    @endif
                                                @endforeach             
                                                <th style="text-align: center">Deduction<br>Absent</th>
                                                <th style="text-align: center">Deduction<br>Late</th>
                                                <th>Earn for period</th>
                                                <th>Total Ded.</th>
                                                <th>Net amount</th>
                                                <th width="7%">Action</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $no = 1;
                                        $totalgrossincome = 0;
                                        $totalalldeduction = 0;
                                        $totalgrossincome1st = 0;
                                        $totalabsences = 0;
                                        $totallate = 0;
                                        $totaltax1 = 0;
                                        $totaltax2 = 0;
                                        $totalnsca_mpc = 0;
                                        $totalprojects = 0;
                                        $totalgrad_guarantor = 0;
                                        $totalearnperiod = 0;
                                        @endphp
                                      
                                        @foreach ($pfiles as $data)
                                          @php
                                          $saltype = $data->sal_type;
                      
                                          $absent = $data->add_less_abs;
                                          $late = $data->less_late;
                                          $tax1 = $data->tax1;
                                          $tax2 = $data->tax2;
                                          $nsca_mpc = $data->nsca_mpc; 
                                          $grad_guarantor = $data->grad_guarantor;
                                          $projects = $data->projects;
                                          $jo_sss = $data->jo_sss;
                      
                                          $grossincome = $data->salary_rate;
                                          
                                          $totaldeduction = $projects + $nsca_mpc + $grad_guarantor + $tax1 + $tax2 + $jo_sss;
                                          $earnperiod = $data->salary_rate;
                                          $netamountrec = ($earnperiod) - $totaldeduction;
                      
                                          $totalgrossincome += $grossincome;
                                          $totalalldeduction += $totaldeduction;
                                          $totalabsences += $absent;
                                          $totallate += $late;
                                          $totalearnperiod += $earnperiod; 
                                          $totaltax1 += $tax1;
                                          $totaltax2 += $tax2;
                                          $totalnsca_mpc += $nsca_mpc;
                                          $totalprojects += $projects;
                                          $totalgrad_guarantor += $grad_guarantor;
                      
                      
                                          $rowEarnSum = 0;
                      
                                          $rowEarns = round(($grossincome) - $totaldeduction, 2);
                                          $decimalPoint = ($rowEarns * 100) % 100;
                                          
                                          $rowEarn = $rowEarns;
                                        
                                          $rowEarn = isset($rowEarn) ? $rowEarn : null;
                      
                                          $rowEarnsArray[] = $rowEarn === null ? '0.00' : $rowEarn;
                      
                                          $rowEarnSum = array_sum($rowEarnsArray);
                                          
                                          $halftotal = round($rowEarnSum, 2);
                                          
                      
                                          @endphp
                                          <tr class="tr-data tr-{{ $data->pid }}">
                                            <td style="text-align: center">{{ $no++ }}</td>
                                            <td>{{ $data->emp_ID }}</td>
                                            <td>{{ $data->lname }} {{ $data->fname }} {{ $data->mname }}</td>
                                            <td>Part-Time Instructor</td>
                                            <td>{{ number_format($data->number_hours, 2) }}</td>
                                            <td>{{ number_format($grossincome, 2) }}</td>
                                            @php
                                                $totaljoAdd = 0;  
                                            @endphp
                                          
                                            @foreach ($modify1 as $mody)
                                                @if ($mody->payroll_id == $data->pid && array_key_exists($mody->column, $columns_jo))
                                                    @php
                                                        $modjoTotalAmount = $columns_jo[$mody->column];
                                                    @endphp
                                                    @if ($modjoTotalAmount != 0.00)
                                                        <td>{{ $mody->action === 'Additionals' ? number_format($mody->amount, 2) : '0.00' }}</td>
                                                        @if ($mody->action === 'Additionals')
                                                            @php
                                                                $totaljoAdd += $mody->amount;
                                                                $modcoltotal[$mody->column] = isset($modcoltotal[$mody->column]) ? $modcoltotal[$mody->column] + $mody->amount : $mody->amount;
                                                            @endphp
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                                
                                            <td>{{ number_format($data->add_less_abs, 2) }}</td>
                                            <td>{{ number_format($data->less_late, 2) }}</td>
                                            <td>{{ number_format(($earnperiod + $totaljoAdd) - ($absent + $late), 2) }}</td>
                                            <td>{{ number_format($totaldeduction, 2) }}</td>
                                            <td>{{ number_format(($earnperiod + $totaljoAdd) - ($absent + $late) - $totaldeduction, 2) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" style="height:32px;" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="deductions">
                                                    </button>
                                                    <div class="dropdown-menu" x-out-of-boundaries="" style="">
                                                    <button id="{{ $data->pid }}" value="1" data-date="{{ $firstHalf }}" data-modes="3" data-stat="{{ $data->stat_ID }}" class="dropdown-item deductions">Deduction</button>
                                                    <button id="{{ $data->pid }}" value="2" data-date="{{ $secondHalf }}" data-modes="4" data-stat="{{ $data->stat_ID }}" class="dropdown-item deductions">Additional</button>
                                                    </div>
                                                </div>
                                                <button value="{{ $data->pid }}" type='button' class='btn btn-danger btn-sm deletePayrollFiles'>
                                                    <i class='fas fa-trash'></i>
                                                </button>
                                            </td>
                                            </tr>
                                        @endforeach 
                                      </tbody>  
                                </table>
                            </div>
                        </div>   
                        @foreach($pfiles as $p)

                    @endforeach
                    @if(isset($deduction))
                    <div class="col-10 mt-3">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center">LATE / ABSENCES</th>
                                    <th></th>
                                    <th colspan="2" class="text-center">BIR DEDUCTION</th>
                                    <th></th>
                                    <th colspan="3" class="text-center">OTHER DEDUCTION</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th class="text-center">Total</th>
                                    <th>TAX 1%</th>
                                    <th>TAX 2%</th>
                                    <th class="text-center">Total</th>
                                    <th>NSCA MPC</th>
                                    <th>Graduate</th>
                                    <th>School Project</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ number_format($totalabsences, 2) }}</td>
                                    <td>{{ number_format($totallate, 2) }}</td>
                                    <td class="text-red text-center" width="7%">{{ number_format($totalabsences + $totallate, 2) }}</td>
                                    <td>{{ number_format($totaltax1, 2) }}</td>
                                    <td>{{ number_format($totaltax2, 2) }}</td>
                                    <td class="text-red text-center" width="7%">{{ number_format($totaltax1 + $totaltax2, 2) }}</td>
                                    <td>{{ number_format($totalnsca_mpc, 2) }}</td>
                                    <td>{{ number_format($totalprojects, 2) }}</td>
                                    <td>{{ number_format($totalgrad_guarantor, 2) }}</td>
                                    <td class="text-red text-center" width="7%">{{ number_format($totalnsca_mpc + $totalprojects + $totalgrad_guarantor, 2) }}</td>
                                </tr>
                            </tbody>
                        </table><br>
                        @php
                            $columns_jo = ['Column1' => 0, 'Column2' => 0, 'Column3' => 0, 'Column4' => 0, 'Column5' => 0];
                            $columns_jo1 = ['Column1', 'Column2', 'Column3', 'Column4', 'Column5'];
                        @endphp
                        
                        @php 
                        $totalmodjoTotalAmount = 0;  
                        $no = 1;
                        @endphp
                        @foreach ($modify1 as $mody)
                            @if ($mody->pay_id == $data->payroll_ID && $mody->action == 'Additionals' && array_key_exists($mody->column, $columns_jo))
                                @php
                                    $columns_jo[$mody->column] += $mody->amount;
                                @endphp
                            @endif
                        @endforeach
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-center">ADDITIONAL</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    @foreach ($modify1 as $mody)
                                        @if ($mody->payroll_id == $data->pid && array_key_exists($mody->column, $columns_jo))
                                            <td>{{ $mody->label }}</td>
                                        @endif
                                    @endforeach
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                @foreach ($modify1 as $mody)
                                    @if ($mody->payroll_id == $data->pid && array_key_exists($mody->column, $columns_jo))
                                        @php
                                            $modjoTotalAmount = $columns_jo[$mody->column];
                                            $totalmodjoTotalAmount += $modjoTotalAmount;
                                        @endphp
                                        <td>{{ number_format($modjoTotalAmount, 2) }}</td>
                                    @endif
                                @endforeach
                                    <td style="text-align: center;" class="text-danger">{{ number_format($totalmodjoTotalAmount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table><br>
                    </div>
                    <div class="col-2 mt-3">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center">SUMMARRY</th>
                                </tr>
                                <tr>
                                    <th>GROSS INCOME</th>
                                    <td class="text-red text-center">{{ number_format($totalgrossincome,2) }}</td>
                                </tr>
                                <tr>
                                    <th>ADDITONAL</th>
                                    <td id="totalDeductAll" class="text-red text-center">{{ number_format($totalmodjoTotalAmount,2) }}</td>
                                </tr>
                                <tr>
                                    <th>LATE / ABSENCES</th>
                                    <td class="text-red text-center">{{ number_format($totalabsences + $totallate, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>TOTAL EARN FOR A PERIOD</th>
                                    <td id="totalDeductAll" class="text-red text-center">{{ number_format(($totalgrossincome + $totalmodjoTotalAmount) - ($totalabsences + $totallate),2) }}</td>
                                </tr>
                            </thead>
                        </table><br>
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>BIR DEDUCTION</th>
                                    <td class="text-red text-center">{{ number_format($totaltax1 + $totaltax2,2) }}</td>
                                </tr>
                                <tr>
                                    <th>OTHER DEDUCTION</th>
                                    <td class="text-red text-center">{{ number_format($totalnsca_mpc + $totalprojects + $totalgrad_guarantor, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>TOTAL DEDUCTION</th>
                                    <td class="text-red text-center">{{ number_format($totaltax1 + $totaltax2 + $totalnsca_mpc + $totalprojects + $totalgrad_guarantor, 2) }}</td>
                                </tr>
                                <tr><td></td><td></td></tr>
                                <tr>
                                    <th>TOTAL NET AMOUNT</th>
                                    <td class="text-red text-center">{{ number_format( ($totalgrossincome + $totalmodjoTotalAmount) - ($totalabsences + $totallate + $totalalldeduction), 2) }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
@include('payroll.modals')
<!-- /End Modal -->
@endsection