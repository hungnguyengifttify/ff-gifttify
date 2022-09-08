<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Generate Images Link
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form class="row g-3">
                <div class="col-auto w-50">
                    <input type="text" class="form-control" name="link" id="link" value="{{$link}}" placeholder="Link">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3" name="action" value="get_links">Get links</button>
                </div>
                @if($link != '')
                <div class="col-auto">
                    <button type="submit" class="btn btn-info mb-3" name="action" value="download_csv">CSV</button>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-info mb-3" name="action" value="download_csv_v2">CSV2</button>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-info mb-3" name="action" value="download_csv_v3">Hiep</button>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-info mb-3" name="action" value="download_csv_v4">GTF</button>
                </div>
                @endif
            </form>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach($result as $k => $v)
                {!! str_repeat("&nbsp", ($v['level'] - 1)*5 ) !!}
                @if($v['type'] == 'folder')
                    {{ "[{$v['name']}]" }}
                @else
                    <a href="{{$v['link']}}" target="_blank">{{$v['name']}}</a>
                @endif
                <br/>
            @endforeach
        </div>
    </div>
</x-app-layout>
