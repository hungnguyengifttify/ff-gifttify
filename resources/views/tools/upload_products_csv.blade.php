<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload products to thecreattify.co via CSV file
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="/post_products_csv" method="post" enctype="multipart/form-data" class="row g-3">
                @csrf <!-- {{ csrf_field() }} -->
                <div class="col-auto w-50">
                    <input type="file" class="form-control" name="csv_file" id="csv_file" value="" placeholder="Csv Products File">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3" name="upload" value="upload">Upload</button>
                </div>
            </form>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
