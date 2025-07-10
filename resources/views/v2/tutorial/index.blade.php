@extends('v2.layouts.app')

@section('title', 'Tutorials')

@section('css')
    <style>
        #zero_configuration_table td {
            word-break: break-all;
            max-width: 300px; /* adjust as needed */
            white-space: normal;
        }

        /*#zero_configuration_table th,*/
        /*#zero_configuration_table td {*/
        /*    vertical-align: middle;*/
        /*}*/
    </style>

    <style>
        .client-actions-box {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 100;
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="list-0f">
            <div class="container-fluid">
                <div class="row px-4 py-4">
                    <h1 class="ml-2">Tutorials</h1>
                </div>
                <div class="row px-4 py-4">
                    @php
                        $tutorials = [
                            [
                                'title' => 'How to add client on DesignCRM',
                                'src' => asset('video/v2-1.mp4'),
                            ],
                            [
                                'title' => 'How to assign project to project manager',
                                'src' => asset('video/v2-2.mp4'),
                            ],
                        ];
                    @endphp
                    @foreach($tutorials as $tutorial)
                        <div class="col-md-4 mb-4">
                            <div class="row m-0">
                                <h4>{{$tutorial['title']}}</h4>
                            </div>
                            <div class="row m-0">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="{{asset($tutorial['src'])}}" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')

@endsection
