@extends('admin.layouts.master')
@section('content')
    <section class="container-fluid">
        <div class="card">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-4">
                        <h3 class="intro-y text-lg text-white fw-bold font-medium mt-1 mb-1" >
                            Sales Report Details</h3>
                    </div>
                    <div class="col-8 mt-1">
                        <form action="{{ route('salesReport') }}" method="GET" class="mb-4  d-flex justify-content-end align-items-center">
                            <input type="date" name="start_date" class="form-control mx-2" value="{{ request('start_date') }}" style="max-width: 200px;">
                            <input type="date" name="end_date" class="form-control mx-2" value="{{ request('end_date') }}" style="max-width: 200px;">
                            <button type="submit" class="btn btn-dark text-dark fw-bold mx-2" style="background-color: #ffffff;">Filter</button>
                            <button type="button" class="btn btn-success" onclick="exportTableToExcel('salesTable')">Export To Excel</button>
                        </form>
                    </div>
                </div>

                <!-- Table of Daily Sales -->
                @if(!empty($sales) && count($sales) > 0)
                <table class="table table-bordered" id="salesTable" >
                    <thead class="table-dark">
                        <tr class="text-white text-center">
                        <th>Order ID</th>
                            <th>Order Code</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Instock</th>
                            <th>Sold</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales  as $item)
                        <tr class="text-white text-center">
                        <td>{{ $item->order_id }}</td>
                            <td>{{ $item->order_code }}</td>
                            <td>{{ $item->productname }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->instock }}</td>
                            <td>{{ $item->sold }}</td>
                            <td>{{ $item->created_at->format('j-F-y') }}</td>

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
        function exportTableToExcel(tableId, filename = 'Sales_Report_Details.xlsx') {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, filename);
        }
     </script>


@endsection
