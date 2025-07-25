@extends('admin.layouts.master')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">Category List</h6>
                    </div>
                    <div class="font-weight-bold text-primary">
                        <a href="{{ route('categoryCreatePage') }}"><i class="fa-solid fa-plus"></i> Add Category</a>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center text-white">
                                <th >ID</th>
                                <th >Name</th>
                                <th >Created Date</th>
                                <th >Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr class="text-center text-white">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->created_at->format('j-F-Y') }}</td>
                                    <td>
                                        <a href="{{ route('categoryEdit', $item->id) }}"
                                            class="btn btn-info"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="{{ route('categoryDelete', $item->id) }}" class="btn btn-danger"><i
                                                class="fa-solid fa-trash-can"></i></a>
                                    </td>
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
