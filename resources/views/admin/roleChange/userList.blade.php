@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <form action="{{ route('userList') }}" method="get">
                                <div class="input-group mb-3">
                                    <input type="text" name="searchKey" class="form-control " placeholder="Search..."
                                        value="{{ request('searchKey') }}">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </form>
                        </h6>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <a href="{{ route('adminList') }}" class="btn btn-secondary mr-2">Admin List <span
                            class="badge badge-light">{{ $adminCount }}</span></></a>
                    <a href="{{ route('userList') }}" class="btn btn-secondary">Customer List <span
                            class="badge badge-light">{{ $data->total() }}</span></a>

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center text-white">
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                @if (auth()->user()->role == 'superadmin')
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr class="text-center text-white">
                                    <td class="text-info font-weight-bold">
                                        @if ($item->name != null)
                                            <a href="{{ route('accountProfile', $item->id) }}"> {{ $item->name }}</a>
                                        @endif
                                        <a href="{{ route('accountProfile', $item->id) }}"> {{ $item->nickname }}</a>
                                    </td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->address }}</td>

                                    @if (auth()->user()->role == 'superadmin')
                                        <td>
                                            <a href="{{ route('deleteAdminAccount', $item->id) }}">
                                                <button class="btn btn-sm btn btn-danger"><i
                                                        class="fa-solid fa-trash-can"></i></button>
                                            </a>
                                            <a href="{{ route('changeAdminRole', $item->id) }}">
                                                <button class="btn btn-sm btn btn-dark text-white">Change to Admin role<i
                                                        class="p-1 fa-solid fa-arrow-up"></i></button>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <span class="d-flex justify-content-end">{{ $data->links() }}</span>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
