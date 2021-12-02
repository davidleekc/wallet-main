@extends('backend.layouts.app')

@section('title') {{ $module_action }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item route='{{route("backend.$module_name.index")}}' icon='{{ $module_icon }}' >
        {{ $module_title }}
    </x-backend-breadcrumb-item>
    <x-backend-breadcrumb-item type="active">{{ $module_action }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-8">
                <h4 class="card-title mb-0">
                    <i class="{{ $module_icon }}"></i> {{ $module_title }} <small class="text-muted">{{ $module_action }}</small>
                </h4>
                <div class="small text-muted">
                    {{ ucwords($module_name) }} Management Dashboard
                </div>
            </div>
            <!--/.col-->
            <div class="col-4">
                <div class="float-right">
                    <a href="{{ route("backend.$module_name.index") }}" class="btn btn-secondary mt-1 btn-sm" data-toggle="tooltip" title="{{ ucwords($module_name) }} List"><i class="fas fa-list"></i> List</a>
                    <!--
                    @can('edit_'.$module_name)
                    <a href="{{ route("backend.$module_name.edit", $$module_name_singular) }}" class="btn btn-primary mt-1 btn-sm" data-toggle="tooltip" title="Edit {{ Str::singular($module_name) }} "><i class="fas fa-wrench"></i> Edit</a>
                    @endcan
                    -->
                </div>
            </div>
            <!--/.col-->
        </div>
        <!--/.row-->

        <hr>

        <div class="row mt-4">
            <div class="col-12 col-sm-5">

                @include('backend.includes.show')

            </div>
            <div class="col-12 col-sm-7">
                <!--
                <div class="text-center">
                    <a href="{{route("backend.$module_name.show", [encode_id($$module_name_singular->id), $$module_name_singular->slug])}}" class="btn btn-success" target="_blank"><i class="fas fa-link"></i> Public View</a>
                </div>
                -->



                <div class="card">
                    <div class="card-header">
                        Client Profile
                    </div>
                    <table class="table table-hover">
                        @forelse($clientprofile as $profile)
                        <tr>
                            <th>{{ __('Avatar') }}</th>
                            <td><img src="{{asset( "img/default-avatar.jpg" )}}" class="user-profile-image img-fluid img-thumbnail" style="max-height:200px; max-width:200px;" /></td>
                        </tr>

                        <tr>
                            <th>{{  __('Id')}}</th>
                            <td>{{ $profile->client_id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('First Name') }}</th>
                            <td>{{ $profile->first_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Last Name') }}</th>
                            <td>{{ $profile->last_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Country') }}</th>
                            <td>{{ $profile->country }}</td>
                        </tr>
                        <tr>
                            <th>{{ $profile->identity_type }}</th>
                            <td>{{ $profile->identity_no }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $profile->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $profile->created_at }}<br><small>({{ $profile->created_at->diffForHumans() }})</small></td>
                        </tr>

                        <tr>
                            <th>{{ __('Updated At') }}</th>
                            <td>{{ $profile->updated_at }}<br/><small>({{ $profile->updated_at->diffForHumans() }})</small></td>
                        </tr>
                        @empty
                            <p class="text-center">
                                Profile not created.
                            </p>
                        @endforelse

                    </table>
                </div>
            </div>


            
            <div class="col-12 col-sm-12">
                <!--
                <div class="text-center">
                    <a href="{{route("backend.$module_name.show", [encode_id($$module_name_singular->id), $$module_name_singular->slug])}}" class="btn btn-success" target="_blank"><i class="fas fa-link"></i> Public View</a>
                </div>
                -->
                <div class="card">
                    <div class="card-header">
                        Transaction
                    </div>
                    <table id="transaction-table" class="table table-hover">
                        <tr>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Balance') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('description') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                        @forelse($clienttransaction as $transaction)             
                        <tr>
                            <td>{{ $transaction->uuid }}</td>
                            <td>{{ $transaction->type }}</td>
                            <td>RM {{ number_format($transaction->amount/100, 2) }}</td>
                            @switch($transaction->type)
                                @case('deposit')
                                    @if(!empty($transaction->meta['r_balance'] ))
                                        <td>RM {{ number_format($transaction->meta['r_balance'] /100, 2) }}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                @break
                                
                                @case('withdraw')
                                    @if(!empty($transaction->meta['p_balance']))
                                        <td>RM {{ number_format($transaction->meta['r_balance'] /100, 2) }}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                @break
                            @endswitch
                            
                            @switch($transaction->confirmed)
                                @case(1)
                                    <td>Success</td>
                                @break
                                @case(2)
                                    <td>Awaiting</td>
                                @break
                                @case(3)
                                    <td>Fail</td>
                                @break
                            @endswitch
                            
                            <td>{{ $transaction->meta['title'] }}</td>
                            <td>{{ $transaction->created_at }}<br><small>({{ $transaction->created_at->diffForHumans() }})</small></td>
                        </tr>
                        
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Transaction Not Found.
                            </td>
                        </tr>
                        @endforelse

                    </table>
                </div>
                <div class="row">
                    <div class="col-7">
                        <div class="float-left">
                            {!! $clienttransaction->total() !!} {{ __('labels.backend.total') }}
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="float-right">
                            {!! $clienttransaction->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col">
                <small class="float-right text-muted">
                    Updated: {{$$module_name_singular->updated_at->diffForHumans()}},
                    Created at: {{$$module_name_singular->created_at->isoFormat('LLLL')}}
                </small>
            </div>
        </div>
    </div>
</div>

@endsection

@push ('after-styles')
<!-- DataTables Core and Extensions -->
<style>
    #transaction-table td:nth-child(3),td:nth-child(4) {
    text-align: right;
}
</style>
@endpush