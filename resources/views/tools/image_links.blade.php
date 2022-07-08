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
                    <button type="submit" class="btn btn-primary mb-3">Get links</button>
                </div>
            </form>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach($result as $k1 => $v1)
                @if($k1 == 'folders')
                    @foreach($v1 as $k2 => $v2)

                    @endforeach
                @endphp
            @endforeach
        </div>
    </div>
</x-app-layout>
