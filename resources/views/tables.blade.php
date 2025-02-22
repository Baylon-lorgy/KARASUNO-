@extends('layouts.dashboardlayout')

@section('title', 'Tables')

@section('content')

<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Reports</h6>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Here you can manage and view reports.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Generate Reports</h6>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Use this section to generate new reports.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
