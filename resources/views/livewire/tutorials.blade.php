<div>
    {{--    @include('livewire.loader')--}}
    <style>
        .table h5 {
            margin: 0px !important;
            line-height: 33px !important;
        }

        tbody {
            display: block;
            max-height: 400px;
            overflow-y: auto;
        }

        thead, tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
    </style>

    <div class="breadcrumb">
        <h1 class="mr-2">Revenue</h1>
    </div>

    <div class="separator-breadcrumb border-top"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div id="accordion">
                @foreach(get_crm_tutorials() as $key => $tutorial)
                    <div class="card">
                        <div class="card-header" id="headingOne_{{$key}}" data-toggle="collapse" data-target="#collapseOne_{{$key}}" aria-expanded="true" aria-controls="collapseOne_{{$key}}" style="cursor: pointer;">
                            <h5 class="mb-0">
                                <button class="btn btn-link">
                                    #{{$key + 1}}: {{$tutorial['title']}}
                                </button>
                            </h5>
                        </div>

                        <div id="collapseOne_{{$key}}" class="collapse" aria-labelledby="headingOne_{{$key}}" data-parent="#accordion">
                            <div class="card-body">
                                <video controls src="{{$tutorial['src']}}" style="width: 100%;"></video>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
