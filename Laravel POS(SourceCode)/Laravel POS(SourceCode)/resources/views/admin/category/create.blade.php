@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col-lg-6 col-md-8 col-12 mx-auto">
            <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark text-center">Add Category Page</h6>
            </div>

            <form action="{{ route('categoryCreate') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Category Name</label>
                        <input type="text" name="category" value="{{ old('category') }}"
                            class="form-control @error('category') is-invalid @enderror" id="exampleFormControlInput1"
                            placeholder="Name...">
                        @error('category')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <input type="submit" value="Create" class="btn btn-primary w-100">
                        </div>
                        <div class="col-6">
                            <a href="{{ route('categoryList') }}" class="btn btn-secondary w-100 text-center">Back</a>
                        </div>
                    </div>
                </div>
            </form>

        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
