@extends('admin.modal')

@section('content')
<div class="page-body">
    <div class="container-fluid">        
      <div class="page-title">
        <div class="row">
          <div class="col-6">
            <h3>Client</h3>
          </div>
          <div class="col-6">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.html"><i data-feather="home"></i></a></li>
              <li class="breadcrumb-item">Dashboard</li>
              <li class="breadcrumb-item active">Client</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row second-chart-list third-news-update">
            <div class="container">
                <div class="justify-content-center">
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <p>{{ \Session::get('success') }}</p>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            @can('role-create')
                                <span class="float-right">
                                    <a class="btn btn-primary" href="{{ route('clients.index') }}">Back</a>
                                </span>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <th>ID</th>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone'</th>
                                            <td>{{ $user->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pin</th>
                                            <td>******</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <th>Client Profile</th>
                                        </tr>
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
                                        <tr>
                                            <td class="text-center">
                                                Profile not created.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>      
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>

@endsection