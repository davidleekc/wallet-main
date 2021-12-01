@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-8">
                <h4 class="card-title mb-0">Transaction List</h4>
                <div class="small text-muted">{{ date_today() }}</div>
            </div>

            <!-- <div class="col-sm-4 hidden-sm-down">
                <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                    <button type="button" id="jokes"class="btn btn-info float-right">
                        <i class="c-icon cil-bullhorn"></i>
                    </button>
                </div>
            </div> -->
        </div>
        <hr>

        <!-- Dashboard Content Area -->
        <div class="row">
            <div class="col">
                <table id="datatable" class="table table-bordered table-hover table-responsive-sm">
                    <thead>
                    <tr>
                        <th class="text-muted text-small text-uppercase">ID</th>
                        <th class="text-muted text-small text-uppercase">Reference No</th>
                        <th class="text-muted text-small text-uppercase">Phone</th>
                        <th class="text-muted text-small text-uppercase">Type</th>
                        <th class="text-muted text-small text-uppercase">Method</th>
                        <th class="text-muted text-small text-uppercase">Amount</th>
                        <th class="text-muted text-small text-uppercase">Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
        <!-- / Dashboard Content Area -->
    </div>
</div>
<!-- / card -->
@endsection

@push ('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<style>
    #datatable td:nth-child(6),td:nth-child(7) {
    text-align: right;
}
</style>
@endpush

@push ('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.4/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>   
<script type="text/javascript">

    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        responsive: true,
        ajax: {
            url: '{{ route("backend.$module_name.index") }}',
            datatype: "json",
            data: function (d) {
                d.search = $('input[type="search"]').val()
            }
        },  
        aaSorting: [[0, "desc"]],
        columns: [
            {data: 'id', name: 'id'},
            {data: 'uuid', name: 'uuid'},
            {data: 'phone', name: 'phone'},
            {data: 'meta.title', name: 'meta.title'},
            {data: 'meta', name: 'meta.payment_method'},
            {data: 'amount', name: 'amount'},
            {data: 'meta', name: 'meta.r_balance'},
        ],
        columnDefs: [

            {
                targets: 4,
                render: function (data, type, row, meta) {

                    if(data.payment_method){
                        return data.payment_method;
                    }else{
                        return '-';
                    }   
                    
                },
            },
            {
                targets: 5,
                render: function (data, type, row, meta) {

                    if(data){
                        return 'RM ' + (data/100).toFixed(2);
                    }else{
                        return '-';
                    }   
                    
                },
            },
            {
                targets: 6,
                render: function (data, type, row, meta) {
                    if(row.type == 'deposit'){
                        //temp
                        if(data.r_balance){

                            return 'RM ' + (data.r_balance/100).toFixed(2);

                        }else{

                            return '-';

                        }
                        
                    }else if(row.type == 'withdraw'){

                        return 'RM ' + (data.p_balance/100).toFixed(2);

                    }else{

                        return '-';
                    }
                    
                },
            }
            
        ],
        dom:    
            "<'ui stackable grid'"+
                "<'row'"+
                    "<'col-6'l>"+
                    "<'col-6'f>"+
                ">"+
                "<'row dt-table'"+
                    "<'col'tr>"+
                ">"+
                "<'row'"+
                    "<'col-12'i>"+
                ">"+
                "<'row'"+
                    "<'col-6 d-flex align-items-center'B>"+
                    "<'col-6 d-flex align-items-start justify-content-end'p>"+
                ">"+
            ">",  
        buttons: [
            //'copy', 'csv', 'excel', 'pdf'
            {
                extend: "copy",  
                //className: "btn-success",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6]
                },
                text:      '<button type="button" class="btn btn-info ml-1"><i class="c-icon cil-copy"></i></button>',
                titleAttr: 'copy'
            },
            {
                extend: "csv", 
                //className: "btn-info",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6] 
                },
                text:      '<button type="button" class="btn btn-success ml-1"><i class="c-icon cil-grid"></i></button>',
                titleAttr: 'csv'
            },
            // {
            //     extend: "excel", 
            //     //className: "btn-info",
            //     exportOptions: {
            //         columns: [ 0, 1, 2, 3, 4, 5, 6] 
            //     },
            //     text:      '<button type="button" class="btn btn-danger mt-1"><i class="c-icon cil-grid"></i></button>',
            //     titleAttr: 'Excel'
            // },
            {
                extend: "pdf", 
                //className: "btn-primary",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6] 
                },
                text:      '<button type="button" class="btn btn-danger ml-1"><i class="c-icon cil-file"></i></button>',
                titleAttr: 'PDF'
            }
        ],
            
        
    });

</script>
@endpush