<style>
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ads Creatives - {{strtoupper($params['type'])}} : {{strtoupper($params['code'])}} - Store {{strtoupper($params['store'])}} - From {{$params['fromDate']}} to {{$params['toDate']}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="row">
                    @foreach ($creatives as $creative)
                        <figure class="figure p-3 col-md-3 border">
                            <small style="font-size: 0.65em">{!! $creative['body'] !!}</small>
                            <a href="{!! $creative['ads_url'] !!}" target="_blank" class="small">
                                <img src="{!! $creative['image_url'] ? $creative['image_url'] : "https://tools.gifttify.com/no-image.jpeg"; !!}" class="figure-img img-fluid rounded" >
                            </a>
                            <figcaption class="figure-caption"><a href="{!! $creative['ads_url'] !!}" target="_blank">{!! $creative['title'] !!}</a></figcaption>
                            <div style="font-size: 0.65em">
                                Total Campaign: {{$creative['countCampaign']}}<br/>
                                Total Spend: {{gifttify_price_format($creative['totalSpendCampaign'])}}<br/>
                                CPC: {!!round($creative['cpc'],2)!!}
                            </div>
                        </figure>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
