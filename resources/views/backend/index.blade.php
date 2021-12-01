@extends('backend.layouts.app')

@section('title') @lang("Dashboard") @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs/>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-8">
                <h4 class="card-title mb-0">@lang("Welcome to", ['name'=>config('app.name')])</h4>
                <div class="small text-muted">{{ date_today() }}</div>
            </div>

            <div class="col-sm-4 hidden-sm-down">
                <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                    <button type="button" id="jokes"class="btn btn-info float-right">
                        <i class="c-icon cil-bullhorn"></i>
                    </button>
                </div>
            </div>
        </div>
        <hr>

        <!-- Dashboard Content Area -->
        <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-value-lg">{{ $clients }}</div>
                    <div>Total Client</div>
                    <div class="progress progress-xs my-2">
                        <div class="progress-bar bg-gradient-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div><small class="text-muted">Widget helper text</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-value-lg">{{ $transactions }}</div>
                    <div>Today Transactions</div>
                    <div class="progress progress-xs my-2">
                        <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div><small class="text-muted">Widget helper text</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-value-lg">RM{{ number_format($revenueToday/100 , 2) }}</div>
                    <div>Today Revenue</div>
                    <div class="progress progress-xs my-2">
                        <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div><small class="text-muted">Widget helper text</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-value-lg">2</div>
                    <div>Issues</div>
                    <div class="progress progress-xs my-2">
                        <div class="progress-bar bg-gradient-danger" role="progressbar" style="width: 89%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div><small class="text-muted">Widget helper text</small>
                </div>
            </div>
        </div>

    </div>
        <!-- <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-gradient-primary">
                    <div class="card-body">
                        <div class="text-value-lg">89.9%</div>
                        <div>Widget title</div>
                        <div class="progress progress-white progress-xs my-2">
                            <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div><small class="text-muted">Widget helper text</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-gradient-warning">
                    <div class="card-body">
                        <div class="text-value-lg">12.124</div>
                        <div>Widget title</div>
                        <div class="progress progress-white progress-xs my-2">
                            <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div><small class="text-muted">Widget helper text</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-gradient-danger">
                    <div class="card-body">
                        <div class="text-value-lg">$98.111,00</div>
                        <div>Widget title</div>
                        <div class="progress progress-white progress-xs my-2">
                            <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div><small class="text-muted">Widget helper text</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-gradient-info">
                    <div class="card-body">
                        <div class="text-value-lg">2 TB</div>
                        <div>Widget title</div>
                        <div class="progress progress-white progress-xs my-2">
                            <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div><small class="text-muted">Widget helper text</small>
                    </div>
                </div>
            </div>

        </div> -->
    </div>
    <!-- / Dashboard Content Area -->
</div>

<div class="row">
    <div class="col-sm-6 col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="card-title mb-0">This is line chart</h4>
                    </div>
                    <div class="col-sm-4 hidden-sm-down">
                        <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                            <button type="button" id="line-remove"class="btn btn-info float-right">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
                <hr>
                <canvas id="myChart" width="200" height="60"></canvas>    
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="card-title mb-0">This is bar chart</h4>
                    </div>
                    <div class="col-sm-4 hidden-sm-down">
                        <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                            <button type="button" id="bar-week"class="btn btn-info float-right">
                                This Week
                            </button>
                            <button type="button" id="bar-month"class="btn btn-info float-right ml-1">
                                This Month
                            </button>
                        </div>
                    </div>
                </div>
                <hr>
                <canvas id="myBarChart" width="200" height="60"></canvas>     
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="card-title mb-0">This is doughnut chart</h4>
                    </div>
                </div>
                <hr>
                <canvas id="myDoughnut" width="200" height="60"></canvas>  
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="card-title mb-0">This is bubble chart</h4>
                    </div>
                    <div class="col-sm-6 hidden-sm-down">
                        <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                            <button type="button" id="bubble-week"class="btn btn-info float-right">
                                This Week
                            </button>
                            <button type="button" id="bubble-month"class="btn btn-info float-right ml-1">
                                This Month
                            </button>
                        </div>
                    </div>
                </div>
                
                <hr>
                <canvas id="myBubble" width="200" height="60"></canvas>       
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="card-title mb-0">This is polar chart</h4>
                    </div>
                </div>
                <hr>
                <canvas id="myPolar" width="200" height="60"></canvas>           
            </div>
        </div>
    </div>
</div>
<!-- / card -->



@endsection
@push('after-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
<script>
    
var client_data = '{{ $clients}}';
var transaction_data = '{{ $transactions }}';
var revenue_data = '{{ number_format($revenueToday/100 , 2) }}';

//Line Chart
const ctx = document.getElementById('myChart');
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Client', 'Transaction', 'Revenue Today', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [ client_data , transaction_data, revenue_data, 5, 2, 3],
            backgroundColor: [
                //'rgba(255, 99, 132, 0.2)',
                //'rgba(54, 162, 235, 0.2)',
                //'rgba(255, 206, 86, 0.2)',
                //'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                //'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                //'rgba(255, 99, 132, 1)',
                //'rgba(54, 162, 235, 1)',
                //'rgba(255, 206, 86, 1)',
                //'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                //'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        animations: {
            tension: {
                duration: 1000,
                easing: 'linear',
                from: 1,
                to: 0,
                loop: true
            }
        }
    }
});

//bar Chart
const ctx2 = document.getElementById('myBarChart');
const myBarChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Client', 'Transaction', 'Revenue Today', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [ client_data , transaction_data, revenue_data, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        animations: {
            tension: {
                duration: 1000,
                easing: 'linear',
                from: 1,
                to: 0,
                loop: true
            }
        }
    }
});

const DATA_COUNT = 5;
const NUMBER_CFG = {count: DATA_COUNT, min: 0, max: 100};

const data = {
  labels: [
    'Red',
    'Blue',
    'Yellow'
  ],
  datasets: [{
    label: 'My First Dataset',
    data: [300, 50, 100],
    backgroundColor: [
      'rgb(255, 99, 132)',
      'rgb(54, 162, 235)',
      'rgb(255, 205, 86)'
    ],
    hoverOffset: 4
  }]
};

//doughnut
const myDoughnut =  new Chart(document.getElementById('myDoughnut'),{
  type: 'doughnut',
  data: data,
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Chart.js Doughnut Chart'
      }
    }
  },
});

const bubble_data = {
  datasets: [{
    label: 'First Dataset',
    data: [{
      x: 20,
      y: 30,
      r: 15
    }, {
      x: 40,
      y: 10,
      r: 10
    }],
    backgroundColor: 'rgb(255, 99, 132)'
  }]
};
//bubble
const myBubble =  new Chart(document.getElementById('myBubble'),{
  type: 'bubble',
  data: bubble_data,
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Chart.js Bubble Chart'
      }
    }
  },
});
const polar_data = {
  labels: [
    'Red',
    'Green',
    'Yellow',
    'Grey',
    'Blue'
  ],
  datasets: [{
    label: 'My First Dataset',
    data: [11, 16, 7, 3, 14],
    backgroundColor: [
      'rgb(255, 99, 132)',
      'rgb(75, 192, 192)',
      'rgb(255, 205, 86)',
      'rgb(201, 203, 207)',
      'rgb(54, 162, 235)'
    ]
  }]
};
//polar area
const myPolar = new Chart(document.getElementById('myPolar'),{
  type: 'polarArea',
  data: polar_data,
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Chart.js Polar Area Chart'
      }
    }
  },
});

$(document).on('click', '#jokes', function(e) {
    {
        //alert("Hello World");
        //validation code to see State field is mandatory.  
        myBarChart.data.datasets[0].data[2] = 50; // Would update the first dataset's value of 'March' to be 50
        myBarChart.update();
    }
}); 

$(document).on('click', '#bar-week', function(e) {
    {
        //alert("Hello World");
        //validation code to see State field is mandatory.  
        myBarChart.data.datasets[0].data[2] = 20; // Would update the first dataset's value of 'March' to be 50
        myBarChart.data.datasets[0].data[3] = 15;
        myBarChart.data.datasets[0].data[4] = 30;
        myBarChart.data.datasets[0].data[5] = 40;
        myBarChart.update();
    }
}); 

$(document).on('click', '#bar-month', function(e) {
    {
        //alert("Hello World");
        //validation code to see State field is mandatory.  
        myBarChart.data.datasets[0].data[2] = 120; // Would update the first dataset's value of 'March' to be 50
        myBarChart.data.datasets[0].data[3] = 210;
        myBarChart.data.datasets[0].data[4] = 60;
        myBarChart.data.datasets[0].data[5] = 90;
        myBarChart.update();
    }
}); 

$(document).on('click', '#bubble-week', function(e) {
    {
        //alert("Hello World");
        //validation code to see State field is mandatory.  
        myBubble.data.datasets[0].data = [{x:10, y:20, r:5}, {x:20,y:30,r:10}];
        //myBubble.data.datasets[0].data[1] = 130, 140, 150;
        //myBubble.data.datasets[0].data[2] = 230, 340, 50;
        myBubble.update();
    }
}); 

$(document).on('click', '#bubble-month', function(e) {
    {
        //alert("Hello World");
        //validation code to see State field is mandatory.  
        myBubble.data.datasets[0].data = [{x:40, y:50, r:5}, {x:220,y:230,r:10}];
        //myBubble.data.datasets[0].data[1] = 30, 240, 50;
        //myBubble.data.datasets[0].data[2] = 30, 140, 50;
        myBubble.update();
    }
}); 

$(document).on('click', '#line-remove', function(e) {
    {
        //alert("Hello World");
        //validation code to see State field is mandatory.  
        //myBubble.data.datasets[0].data = [{x:40, y:50, r:5}, {x:220,y:230,r:10}];
        //myBubble.data.datasets[0].data[1] = 30, 240, 50;
        //myBubble.data.datasets[0].data[2] = 30, 140, 50;
        myChart.destroy();
    }
}); 
</script>
@endpush