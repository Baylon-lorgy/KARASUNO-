@extends('layouts.viewdashboardlayout')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            <p class="mb-4"></p>
        </div>
        <div class="col-xl-3 col-sm-6 mb-2">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Today's Storage</p>
                            <h4 class="mb-0">%example</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">weekend</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+55% </span>than last week</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-2">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Today's Water level</p>
                            <h4 class="mb-0">%example</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">person</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+3% </span>than last month</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-2">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Soil moisture level</p>
                            <h4 class="mb-0">%example</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">leaderboard</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-danger font-weight-bolder">-2% </span>than yesterday</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-2">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Air Quality</p>
                            <h4 class="mb-0">%example</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">air</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-warning font-weight-bolder">+10% </span>than last week</p>
                </div>
            </div>
        </div>

        
        
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Storage Capacity</h6>
                    <p class="text-sm">Water Flow Valves</p>
                    <div class="pe-2">
                        <div class="chart">
                            <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                        <p class="mb-0 text-sm">campaign sent 2 days ago</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Humidity</h6>
                    <p class="text-sm">Water vapor</p>
                    <div class="pe-2">
                        <div class="chart">
                            <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                        <p class="mb-0 text-sm">updated 4 min ago</p>
                    </div>
                </div>
            </div>
        </div>

        <b></b>

        <div class="col-lg-4 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Soil Moisture</h6>
                    <p class="text-sm">Water content of soil</p>
                    <div class="pe-2">
                        <div class="chart">
                            <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                        <p class="mb-0 text-sm">just updated</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0">Humidity</h6>
                    <p class="text-sm">Water Flow Valves</p>
                    <div class="pe-2">
                        <div class="chart">
                            <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                        <p class="mb-0 text-sm">campaign sent 2 days ago</p>
                    </div>
                </div>
            </div>
        </div>

        

    </div>
</div>

  <!-- Facebook Page Plugin beside Humidity card -->
  <div class="col-lg-4 col-md-6 mb-2">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-body">
                <!-- Facebook Page Plugin -->
                <div class="fb-page" 
                    data-href="https://www.facebook.com/profile.php?id=100068682045391"
                    data-tabs="timeline"
                    data-width="500"
                    data-height="650"
                    data-small-header="false"
                    data-adapt-container-width="true"
                    data-hide-cover="false"
                    data-show-facepile="true">
                </div>
                
                <!-- Facebook SDK -->
                <script async defer crossorigin="anonymous" 
                    src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v16.0">
                </script>
            </div>
        </div>
    </div>

@endsection