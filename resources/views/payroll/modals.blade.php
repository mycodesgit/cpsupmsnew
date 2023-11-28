@php
    $current_route=request()->route()->getName();
@endphp
@if($current_route == "viewPayroll")
<div class="modal fade" id="modal-addpayroll">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus"></i> Add New
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form  class="form-horizontal" method="POST" action="{{ route('createPayroll') }}">
                    @csrf
                    <input type="hidden" name="campID" value="{{ $campId }}">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Statuses:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    </div>
                                    <select class="form-control" id="stat-name" name="statName" onchange="checkStat(this.value)" required>
                                        <option value=""> --- Select Here --- </option>
                                        @foreach ($status as $stat)
                                            <option value="{{ $stat->id }}">{{ $stat->status_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left statName_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Date Start:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control float-right" name="PayrollDateStart" id="dateStart"  onchange="calculateDays()" required>
                                </div>
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left PayrollDateStart_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Date End:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control float-right" name="PayrollDateEnd" id="dateEnd"  onchange="calculateDays()" required>
                                </div>
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left PayrollDateEnd_error"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="funding">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Funding:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    </div>
                                    <select class="form-control" name="fund">
                                        <option>Income</option>
                                        <option>Yearbook</option>
                                        <option>MDS</option>
                                    </select>
                                </div>
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left statName_error"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Working Days:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="number" name="number_days" step="any" id="working-days" class="form-control float-right" required readonly>
                                </div>
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left PayrollDateStart_error"></span>
                            </div>
                        </div>
                    </div>
                   <!-- First date input -->

                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" name="btn-submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>   
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>
@endif
@if($current_route == "storepayroll" || $current_route == "storepayroll-jo" || $current_route == "storepayroll-partime-jo")
<style>
    .btn-group-toggle .btn {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-group-toggle .btn.active {
        background-color: green;
    }

    .custom-input-group {
        display: flex;
        justify-content: center;
    }

/* Add this CSS style for the checked radio button */
.custom-input-group .btn input[type="radio"]:checked + span {
    background-color: #FF0000; /* Replace 'your-color' with the desired background color */
}

</style>

<div class="modal fade" id="modal-importPayrollsTwo">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus"></i> Add New
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form class="form-horizontal" action="{{ route('importPayrollsTwo', [$payrollID, $statID]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Employees</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-user"></i>
                                        </span>
                                    </div>
                                    <select class="form-control select2" name="emp_ID" style="width: 91%;" required>
                                        <option value=""> --- Select Employee --- </option>
                                        @foreach($employee as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->emp_ID }} - {{ $emp->lname }} {{ $emp->fname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left Status_error"></span>
                            </div>
                        </div>
                    </div> 
                    @if($empStat == "Regular" || $empStat == "Job Order")
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <label for="exampleInputName">No. of Days</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="number" name="number_hours" step="any" min="1" max="{{ $days }}" value="{{ $days }}" placeholder="No. of working Days" class="form-control" required>
                                       </div>    
                                    <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left FirstName_error"></span>
                                </div>
                            </div>
                        </div>
                    @elseif($empStat == "Part-time/JO")
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <label for="exampleInputName">No. of Hours</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clock"></i>
                                            </span>
                                        </div>
                                        <input type="number" name="number_hours" step="any" min="1" placeholder="No. of Hours" class="form-control" required>
                                       </div>    
                                    <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left FirstName_error"></span>
                                </div>
                            </div>
                        </div>
                    @elseif($empStat == "Part-time")  
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName">Select Days/Hours</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar"></i>
                                        </span>
                                    </div>
                                    <select class="form-control select2" name="hr_day" style="width: 91%;" onchange="dayHours(this.value)" required>
                                        <option value=""> --- Select Option --- </option>
                                        <option>Days</option>
                                        <option>Hours</option>
                                    </select>
                                   </div>    
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left FirstName_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="J-work">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="exampleInputName" id="nh"></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-clock"></i>
                                        </span>
                                    </div>
                                    <input type="number" id="input-nh" name="number_hours" step="any" step="any" min="1" class="form-control" required>
                                   </div>    
                                <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left FirstName_error"></span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" name="btn-submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                    </div> 
                </form>
            </div>
            
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-codes">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus"></i> Code
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="row">
                        @foreach($codes as $cd)
                        <div class="col-md-4">
                            <label for="exampleInputName">{{ $cd->code_name }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <input type="checkbox" id="{{ $cd->id }}" name="status" onchange="updateCheckbox(this)" @if(isset($cd->status) && !empty($cd->status)) checked @endif>
                                    </span>
                                </div>
                                <input type="text" id="{{ $cd->id }}" name="code" class="form-control" value="{{ $cd->code }}" onchange="updateCode(this)">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>
            
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-deductions">
    <div id="modal-deduct" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus"></i> Deductions <span class="date-deduct"></span>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form class="form-horizonta" action="{{ route('deductions-update') }}" method="POST">
                    <input type="hidden" id="payroll_id" name="payroll_id" class="form-control">
                    @csrf

                    {{-- Job-Order --}}
                    @if($empStat == "Job Order" || $empStat == "Part-time" || $empStat == "Part-time/JO")
                    <div class="form-group">
                        <h4><strong>BIR DEDUCTIONS</strong></h4>
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="exampleInputName">TAX 3%</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-clipboard"></i>
                                        </span>
                                    </div>
                                    <input type="number" id="tax_one" name="tax_one" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="exampleInputName">TAX 2%</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-clipboard"></i>
                                        </span>
                                    </div>
                                    <input type="number" id="tax_two" name="tax_two" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right" readonly>
                                </div>
                            </div>
                            <div class="cold-md-1">
                                <label for="exampleInputName"></label>
                                <div class="input-group">
                                    <input style="width: 38px; height: 38px; margin-top: 8px;" type="checkbox" id="twocheckbox" name="twocheckbox" class="form-control form-control-lg float-right" value="1">
                                </div>
                            </div>
                    @endif
                    
                    {{-- Regular --}}
                    {{-- Storepayroll --}}
                    @if($empStat == "Regular")
                        <div class="form-group">
                            <h4><strong>GSIS DEDUCTIONS</strong></h4>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="exampleInputName">EML</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="hidden" name="cat" id="catd">
                                        <input type="number" id="eml" name="eml" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">POLICY</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="pol_gfal" name="pol_gfal" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Consol</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="consol" name="consol" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">EDUC. ASST</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="ed_asst_mpl" name="ed_asst_mpl" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">MPL loan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="loan" name="loan" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">RLIP</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="rlip" name="rlip" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">GFAL</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="gfal" name="gfal" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Computer</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="computer" name="computer" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Help</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="health" name="health" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                            </div>
                            <h4 class="pt-3"><strong>PAG-IBIG DEDUCTIONS</strong></h4>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="exampleInputName">MPL</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="mpl" name="mpl" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">PREM.</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="prem" name="prem" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Calamity Loan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="calam_loan" name="calam_loan" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">MP2.</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="mp2" name="mp2" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Housing Loan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="house_loan" name="house_loan" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <h4 class="pt-2"><strong>OTHER PAYABLES</strong></h4>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="exampleInputName">Philhealth</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="philhealth" name="philhealth" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Withholding Tax</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="holding_tax" name="holding_tax" step="any" min="0"onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">LBP</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="lbp" name="lbp" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Cauyan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="cauyan" name="cauyan" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @endif
                                @if($empStat == "Job Order" || $empStat == "Part-time/JO")
                                    <h4 class="pt-2"><strong>OTHER DEDUCTIONS</strong></h4>
                                    <div class="form-row">
                                @endif
                                <div class="col-md-{{$empStat == "Regular" ? '4' : '6'}}">
                                    <label for="exampleInputName">Projects</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="projects" name="projects" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-{{$empStat == "Regular" ? '4' : '6'}}">
                                    <label for="exampleInputName">NSCA MPC</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="nsca_mpc" name="nsca_mpc" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>

                                @if($empStat == "Regular")
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="exampleInputName">Medical Deduction</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="med_deduction" name="med_deduction" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                            @endif
                                <div class="col-md-{{$empStat == "Regular" ? '4' : '6'}}">
                                    <label for="exampleInputName">Graduate Shool {{$empStat == "Regular" ? '/ Guarantor' : ''}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="grad_guarantor" name="grad_guarantor" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @if($empStat == "Regular")
                                <div class="col-md-4">
                                    <label for="exampleInputName">CFI</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="cfi" name="cfi" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="exampleInputName">CSB</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="csb" name="csb" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">FASFEED</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="fasfeed" name="fasfeed" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputName">Disallow ANCE / UNLIQUIDATED</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="dis_unliquidated" name="dis_unliquidated" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                            @endif
                                @if($empStat == "Job Order" || $empStat == "Part-time/JO")
                                <div class="col-md-6">
                                    <label for="exampleInputName">SSS</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="jo_sss" name="jo_sss" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @endif
                                @if($empStat == "Job Order")
                                <div class="col md 6">
                                    <label for="exampleInputName">SMLF Loan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"> 
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="jo_smlf_loan" name="jo_smlf_loan" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-{{$empStat == "Regular" ? '4' : '6'}}">
                                    <label for="exampleInputName">Less Absences @if($empStat == "Regular") 1st @endif</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="add_less_abs" name="add_less_abs" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @if($empStat == "Regular")
                                <div class="col-md-{{$empStat == "Regular" ? '4' : '6'}}">
                                    <label for="exampleInputName">Less Absences @if($empStat == "Regular") 2nd @endif</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="add_less_abs1" name="add_less_abs1" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @endif
                                @if($empStat == "Job Order" || $empStat == "Part-time/JO")
                                <div class="col-md-{{$empStat == "Regular" ? '4' : '6'}}">
                                    <label for="exampleInputName">Less Late</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-clipboard"></i>
                                            </span>
                                        </div>
                                        <input type="number" id="less_late" name="less_late" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                    </div>
                                </div>
                                @endif
                        @if($empStat == "Regular")
                            </div>
                        </div>
                        @endif
                        @if($empStat != "Regular")
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" name="btn-submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
            
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-additional">
    <div id="modal-deduct" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus"></i> Additional <span class="date-add"></span>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form class="form-horizontal" action="{{ route('additional-update') }}" method="POST">
                    <input type="hidden" id="payroll_idd" name="payroll_id" class="form-control">
                    @csrf
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-4">
                                <label for="exampleInputName">SSL Salary Differential</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-clipboard"></i>
                                        </span>
                                    </div>
                                    <input type="hidden" name="cat" id="cat">
                                    <input type="number" id="add_sal_diff" name="add_sal_diff" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="exampleInputName">NBC 461 Salary Differential {{ date('Y'); }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-clipboard"></i>
                                        </span>
                                    </div>
                                    <input type="number" id="add_nbc_diff" name="add_nbc_diff" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="exampleInputName">Step Increment</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-clipboard"></i>
                                        </span>
                                    </div>
                                    <input type="number" id="add_step_incre" name="add_step_incre" step="any" min="0" onchange="if (this.value <= 0) {this.value = '0.00';}" class="form-control float-right">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" name="btn-submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-modification">
    <div id="modal-deduct" class="modal-dialog modal-xl">
        <div class="modal-content">
            
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus"></i> Adjustments  <span class="date-add"></span>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form class="form-horizontal" action="{{ route('modifyUpdate') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="form-row modify-show">
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" name="btn-submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer justify-content-between">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pdfoffice">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-12">
                            <label for="exampleInputName">Office</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-user"></i>
                                    </span>
                                </div>
                                <input type="text" id="halfmon" hidden>
                                <select class="form-control select2" name="emp_ID" style="width: 91%;" onchange="redirectToTargetPage(event)" required>
                                    <option value=""> --- Select Office --- </option>
                                    @foreach($office as $off)
                                        <option data-payrollId="{{ $payrollID }}" data-statId="{{ $statID }}" value="{{ $off->id }}">{{ $off->office_name }}</option>
                                    @endforeach
                                </select>                                                                                     
                            </div>
                            <span style="color: #FF0000; font-size: 10pt;" class="form-text text-left Status_error"></span>
                        </div>
                    </div>
                </div> 
            </div>
            
        </div>
    </div>
</div>

@endif

