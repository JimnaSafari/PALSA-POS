@extends('admin.layouts.master')
@section('content')
    <section class="container-fluid">
        <div class="card">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-4">
                        <h3 class="intro-y text-lg text-white fw-bold font-medium mt-1 mb-1" >
                            Profit and Loss Report</h3>
                    </div>
                    <div class="col-8 mt-1">
                        <form action="{{ route('profitlossReport') }}" method="GET" class="mb-4  d-flex justify-content-end align-items-center">
                            <input type="date" name="start_date" class="form-control mx-2" value="{{ request('start_date') }}" style="max-width: 200px;">
                            <input type="date" name="end_date" class="form-control mx-2" value="{{ request('end_date') }}" style="max-width: 200px;">
                            <button type="submit" class="btn btn-dark text-dark fw-bold mx-2" style="background-color: #ffffff;">Filter</button>
                            <button type="button" class="btn btn-success" onclick="exportTableToExcel('salesTable')">Export To Excel</button>
                        </form>
                    </div>
                </div>

                <!-- Table of Daily Sales -->
                @if(!empty($productsales) && count($productsales) > 0)
                <table class="table table-bordered" id="salesTable" >
                    <thead class="table-dark">
                        <tr class="text-white text-center">
                            <th>Product Name</th>
                            <th>Sell Price</th>
                            <th>Purchase Price</th>
                            <th>Units Sold</th>
                            <th>Total Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productsales  as $item)
                        <tr class="text-white text-center">
                            <td>{{ $item->name }}</td>
                            <td>{{ number_format($item->sell_price, 2) }}</td>
                            <td>{{ number_format($item->purchase_price, 2) }}</td>
                            <td>{{ $item->units_sold }}</td>
                            <td>{{ number_format($item->total_profit, 2) }}</td>
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
        function exportTableToExcel(tableId, filename = 'Profit & Lost Report_Details.xlsx') {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, filename);
        }
     </script>


@endsection
