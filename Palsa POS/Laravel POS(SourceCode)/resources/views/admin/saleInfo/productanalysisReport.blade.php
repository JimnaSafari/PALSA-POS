@extends('admin.layouts.master')
@section('content')
    <section class="container-fluid">
        <div class="card">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-4">
                        <h3 class="intro-y text-lg text-white fw-bold font-medium mt-1 mb-1" >
                            Product Analysis Report</h3>
                    </div>
                    <div class="col-8 mt-1">
                        <form action="{{ route('productReport') }}" method="GET" class="mb-4  d-flex justify-content-end align-items-center">
                            <input type="date" name="start_date" class="form-control mx-2" value="{{ request('start_date') }}" style="max-width: 200px;">
                            <input type="date" name="end_date" class="form-control mx-2" value="{{ request('end_date') }}" style="max-width: 200px;">
                            <button type="submit" class="btn btn-dark text-dark fw-bold mx-2" style="background-color: #ffffff;">Filter</button>
                            <button type="button" class="btn btn-success" onclick="exportTableToExcel('salesTable')">Export To Excel</button>
                        </form>
                    </div>
                </div>

                <!-- Table of Daily Sales -->
                @if(!empty($stock) && count($stock) > 0)
                <table class="table table-bordered" id="salesTable" >
                    <thead class="table-dark">
                        <tr class="text-white text-center">
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Category Name</th>
                            <th>Instock</th>
                            <th>Sold</th>
                            <th>Remaining Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stock  as $item)
                        <tr class="text-white text-center">

                            <td>{{ $item->product_id }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->category_name }}</td>
                            <td>{{ $item->in_stock }}</td>
                            <td>{{ $item->units_sold }}</td>
                            <td>{{ $item->remaining_stock }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                @else
                <div class="alert alert-warning" role="alert">
                    <p class="text-center text-dark">There are no data to show within this date.</p>
                </div>
            @endif
            </div>
        </div>
        </div>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- export excel -->
     <script>
        function exportTableToExcel(tableId, filename = 'Product_Analysis_Report_Details.xlsx') {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, filename);
        }
     </script>


@endsection
