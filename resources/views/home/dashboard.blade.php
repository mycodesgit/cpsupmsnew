@extends('layouts.master')

@section('body')
<div class="container-fluid">
    <div class="row" style="padding-top: 100px;">
        <div class="col-lg-9">
            <div class="card card-success card-outline">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $empCount }}</h3>
                                    <p>Employees</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3>{{ $offCount }}</h3>
                                    <p>Offices</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-building"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ count($camp) }}</h3>
                                    <p>Campus</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-success card-outline">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 17pt">Hi! Good day, <b>{{auth()->user()->fname}}</b></h5>
                    <p class="card-text" style="font-size: 15pt"></p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 17pt"><b></b></h5>
                    <p class="card-text" style="font-size: 15pt">
                        
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection