@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col-lg-6 col-md-8 col-12 mx-auto">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark text-center">
                    Update Category
                </h6>
            </div>

            <div class="card-body">
                <form action="{{ route('categoryUpdate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="categoryInput" class="form-label fw-bold">Category Name</label>
                        <input type="hidden" name="categoryID" value="{{ $data->id }}">
                        <input type="text" name="category" value="{{ old('category', $data->name) }}"
                            class="form-control @error('category') is-invalid @enderror" id="categoryInput"
                            placeholder="Drinks...">
                        @error('category')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row mt-4">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('categoryList') }}" class="btn btn-secondary w-100">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
