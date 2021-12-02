@extends('admin.modal')

@section('content')
<div class="page-body">
    <div class="container-fluid">        
      <div class="page-title">
        <div class="row">
          <div class="col-6">
            <h3>Users</h3>
          </div>
          <div class="col-6">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.html"><i data-feather="home"></i></a></li>
              <li class="breadcrumb-item">Dashboard</li>
              <li class="breadcrumb-item active">User</li>
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
                            <span class="float-right">
                                <a class="btn btn-primary" href="{{ route('clients.create') }}">New User</a>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table  class="table table-bordernone">
                                    <thead class="tbl-strip-thad-bdr">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Phone No</th>
                                            <th scope="col">Updated At</th>
                                            <th class="text-end" width="350px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $user)
                                            <tr scope="row">
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->phone }}</td>
                                                <td>{{ $user->updated_at }}</td>
                                                <td class="text-end">
                                                    <a class="btn btn-success" href="{{ route('clients.show',$user->id) }}">Show</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $data->render() }}
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