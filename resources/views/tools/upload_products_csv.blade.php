<x-app-layout>
    <x-slot name="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="text-primary text-decoration-underline nav-link" aria-current="page" href="https://tools.gifttify.com/create_shopify_csv">Shopify CSV</a>
                        </li>
                        <li class="nav-item">
                            <a class="text-primary text-decoration-underline fw-bold nav-link active" href="https://tools.gifttify.com/upload_products_csv">Push to Web</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="/post_products_csv" method="post" enctype="multipart/form-data" class="row g-3">
                @csrf <!-- {{ csrf_field() }} -->
                <div class="col-auto w-50">
                    <input type="file" class="form-control" name="csv_file" id="csv_file" value="" placeholder="Csv Products File">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3" name="upload" value="upload">Push to Web</button>
                </div>

                <div class="col-auto" style="display: none">
                    <button type="submit" class="btn btn-danger mb-3" name="upload" value="delete">Delete Products</button>
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
