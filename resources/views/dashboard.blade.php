<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <button id="reportrange" class="btn btn-primary">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>Today</span> <i class="fa fa-caret-down"></i>
                    </button>

                    <script type="text/javascript">
                        $(function() {

                            var start = moment();
                            var end = moment();

                            $('#reportrange').daterangepicker({
                                "autoApply": true,
                                ranges: {
                                    'Today': [moment(), moment()],
                                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                },
                                "alwaysShowCalendars": true,
                                startDate: start,
                                endDate: end
                            }, function(start, end, label) {
                                console.log(start, end, label);
                                if (label == 'Custom Range') {
                                    $('#reportrange span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
                                } else {
                                    $('#reportrange span').html(label);
                                }
                            });

                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
