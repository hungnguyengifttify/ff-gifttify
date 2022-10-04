<x-app-layout>
    <x-slot name="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="text-primary text-decoration-underline fw-bold nav-link active" aria-current="page" href="https://tools.gifttify.com/create_shopify_csv">Shopify CSV</a>
                        </li>
                        <li class="nav-item">
                            <a class="text-primary text-decoration-underline nav-link" href="https://tools.gifttify.com/upload_products_csv">Push to Web</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="post" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="form-check form-switch" style="margin-left: 10px;">
                    <input class="form-check-input" type="checkbox" name="export_for_shopify" id="export_for_shopify" value="1">
                    <label class="form-check-label" for="export_for_shopify">Export for Shopify</label>
                </div>
                <div class="col-auto w-50">
                    <input type="file" required class="form-control" name="json_file" id="json_file" placeholder="Link">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3" name="action" value="download_csv">Download Csv</button>
                </div>
                <div class="col-auto" style="{!! isset($_REQUEST['test']) && $_REQUEST['test'] == 1 ? '' : 'display:none'; !!}">
                    <button type="submit" class="btn btn-primary mb-3" name="action" value="download_csv_test">Test</button>
                </div>
            </form>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        </div>
    </div>
</x-app-layout>
