<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Campus;

class MasterController extends Controller
{
    //
     public function dashboard(){
        $camp = Campus::all();
        
        if(auth()->user()->campus_id == 1){
            $empCount = Employee::count();
        }
        else{
            $empCount = Employee::where('emp_ID', auth()->user()->campus_id)->count();
        }
        $offCount = Office::count();
        
        return view("home.dashboard")->with("camp", $camp)->with("empCount", $empCount)->with("offCount", $offCount);
    }
    public function logout(){
        auth()->logout();
        return redirect()->route('getLogin')->with('success','You have been Successfully Logged Out');
    }
}
