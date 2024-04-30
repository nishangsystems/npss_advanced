<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{$title ?? ''}} | {{__('text.app_name')}}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{url('/')}}/public/assets/css/*.css" />

    <link rel="stylesheet" href="{{asset('css/app.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <!-- <link rel="stylesheet" href="{{asset('assets/css/*.css')}}" /> -->
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}"/>
    <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" class="ace-main-stylesheet"
          id="main-ace-style"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/responsive.dataTables.min.css">

    <!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script> -->

    <STYLE>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
        }
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            width: 100%;
        }
        .dt-button{
            background-image: none!important;
            border: 1px solid #FFF;
            border-radius: 0;
            padding: 5px 20px;
            border-radius: 5px;
            box-shadow: none!important;
            -webkit-transition: background-color .15s,border-color .15s,opacity .15s;
            -o-transition: background-color .15s,border-color .15s,opacity .15s;
            transition: background-color .15s,border-color .15s,opacity .15s;
            vertical-align: middle;
            margin: 0;
            position: relative;
        }
        table{padding: 0px !important}
        table th, table td{
            padding: 10px;
        }
        .table td{
            border-bottom: 1px  solid  #f1f1f1 !important;
        }
        .nav li {
            display: block;
            width: 100% !important;
        }
        .dropdown-toggle:after {
            display: none;
        }
    </STYLE>
</head>

<body class="no-skin">
    <div class="col-sm-12">
        <div class="bg-white" id="printable">
            <div class="my-5">
                <table class="" id="hidden-table-info">
                    <thead>
                        <div id="letter-head">
                            <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" alt="" class="w-100 img">
                        </div>
                        <tr class="border-bottom border-top">
                            <th colspan="10">
                                <div class="text-center h4 fw-bolder py-3 mt-3 text-capitalize"><b>{{$title}}</b></div>
                            </th>
                        </tr>
                        <tr class="text-capitalize border-bottom bg-dark text-white border-top">
                            <th class="border-left border-right">#</th>
                            <th class="border-left border-right">{{__('text.word_name')}}</th>
                            <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.current_fee')}}</span></th>
                            <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.extra_fee')}}</span></th>
                            {{-- <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.cum_extra_fee')}}</span></th> --}}
                            {{-- <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.past_debt')}}</span></th> --}}
                            <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.current_amount_paid')}}</span></th>
                            {{-- <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.cum_amount_paid')}}</span></th> --}}
                            <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.word_scholarship')}}</span></th>
                            {{-- <th class="border-left border-right"><span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{__('text.cum_amount_owing')}}</span></th> --}}
                            <!-- <th></th> -->
                        </tr>
                    </thead>
                    @php($k = 1)
                    <tbody id="content">
                        @foreach($data['students'] ?? [] as $student)
                            <tr class="border-bottom">
                                <td class="border-left border-right">{{$k++}}</td>
                                <td class="border-left border-right">{{$student['name'] ?? ''}}</td>
                                <td class="border-left border-right">{{$student['current_fee'] ?? ''}}</td>
                                <td class="border-left border-right">{{$student['extra_fee'] ?? ''}}</td>
                                {{-- <td class="border-left border-right">{{$student['cum_extra_fee'] ?? ''}}</td> --}}
                                {{-- <td class="border-left border-right">{{$student['debt'] ?? ''}}</td> --}}
                                <td class="border-left border-right">{{number_format($student['current_paid'] ?? 0)}}</td>
                                {{-- <td class="border-left border-right">{{number_format($student['paid'] ?? 0)}}</td> --}}
                                <td class="border-left border-right">{{number_format($student['scholarship'] ?? 0)}}</td>
                                {{-- <td class="border-left border-right">{{number_format($student['owing'] ?? 0)}}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>
    <script src="{{asset('assets/js/ace.min.js')}}"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net-bs4/js/dataTables.responsive.min.js"></script>


    <script>
        $(function () {
            $('.table , .adv-table table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel',
                    {
                        text: 'Download PDF',
                        extend: 'pdfHtml5',
                        message: '',
                        orientation: 'portrait',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function (doc) {
                            doc.pageMargins = [10,10,10,10];
                            doc.defaultStyle.fontSize = 7;
                            doc.styles.tableHeader.fontSize = 7;
                            doc.styles.title.fontSize = 9;
                            doc.content[0].text = doc.content[0].text.trim();

                            doc['footer']=(function(page, pages) {
                                return {
                                    columns: [
                                        "{{ $title ?? '' }}",
                                        {
                                            // This is the right column
                                            alignment: 'right',
                                            text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                        }
                                    ],
                                    margin: [10, 0]
                                }
                            });
                            // Styling the table: create style object
                            var objLayout = {};
                            // Horizontal line thickness
                            objLayout['hLineWidth'] = function(i) { return .5; };
                            // Vertikal line thickness
                            objLayout['vLineWidth'] = function(i) { return .5; };
                            // Horizontal line color
                            objLayout['hLineColor'] = function(i) { return '#aaa'; };
                            // Vertical line color
                            objLayout['vLineColor'] = function(i) { return '#aaa'; };
                            // Left padding of the cell
                            objLayout['paddingLeft'] = function(i) { return 4; };
                            // Right padding of the cell
                            objLayout['paddingRight'] = function(i) { return 4; };
                            // Inject the object in the document
                            doc.content[1].layout = objLayout;
                        }
                    }

                ],
                info:     false,
                searching: true,
                // order: [
                //     [1, 'asc']
                // ],
            });

        });


    </script>

    <script src="{{ asset('libs')}}/datatables.net/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/jszip.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/pdfmake.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/vfs_fonts.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/buttons.html5.min.js"></script>

    <script>
        (function($){
            'use strict';
            $(window).on('load', function () {
                if ($(".pre-loader").length > 0)
                {
                    $(".pre-loader").fadeOut("slow");
                }
            });
        })(jQuery)
    </script>
    @yield('script')
</body>
</html>