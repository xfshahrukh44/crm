@extends($layout)
@section('title', 'Client detail')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /*img {*/
        /*    max-width: 50px;*/
        /*}*/

        .card-body.text-center {
            min-height: 150px;
        }

        p.text-muted.mt-2.mb-2 {
            font-size: 15px;
        }

        .card-body.text-center:hover {
            box-shadow: 0px 0px 15px 8px #00aeee1a;
        }
    </style>
@endpush
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Client detail</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        {{--brand detail--}}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="row text-center mb-4">
                    <div class="col-md-6 offset-md-3">
                        <h2>{{$client->name . ' ' . $client->last_name}}</h2>
                    </div>
                </div>
                @if($client->contact)
                    <div class="row text-center">
                        <div class="col-md-6 offset-md-3">
                            <h4>
                                <i class="fas fa-phone"></i>
                                <a href="tel:{{$client->contact}}">{{$client->contact}}</a>
                            </h4>
                        </div>
                    </div>
                @endif
                @if($client->email)
                    <div class="row text-center">
                        <div class="col-md-6 offset-md-3">
                            <h4>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:{{$client->email}}">{{$client->email}}</a>
                            </h4>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6]))
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12">
                    <h2 class="ml-3">Invoices ({{count($client->invoices)}})</h2>
                </div>
                @if (count($client->invoices))
                    @php
                        $route = \Illuminate\Support\Facades\Auth::user()->is_employee == 2 ? 'admin.invoice' : 'manager.invoice';
                    @endphp
                    <div class="col-lg-12 col-md-12">
                        <a target="_blank" href="{{route($route, ['client_id' => $client->id])}}" class="btn btn-primary ml-3">View Invoices</a>
                    </div>
                @endif
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-lg-12 col-md-12">
{{--                <h2 class="ml-3">Projects ({{count($client->projects)}})</h2>--}}
                <h2 class="ml-3">Services ({{count($client->projects)}})</h2>
            </div>
        </div>
        <!-- CARD ICON-->
        <div class="row client_wrapper">
            @foreach($projects as $project)
                <div class="col-lg-2 col-md-6 col-sm-6">

                    <a target="_blank" href="{{route('projects.detail', $project->id)}}">
                        <div class="card card-icon mb-4">
                            <div class="card-body text-center">
                                @php
                                    if (Auth::user()->is_employee == 2) {
                                        $active_tasks = \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)->get();
                                        $department_count = count(array_unique(\App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)->get()->pluck('category_id')->toArray())) ?? 0;
                                    } else {
                                        $active_tasks = \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)
                                            ->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list())->get();
                                        $department_count = count(array_unique(\App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list())->get()->pluck('category_id')->toArray())) ?? 0;
                                    }
                                @endphp

                                <p class="text-muted mt-2 mb-2">{{$project->name}}</p>

                                @if($department_count > 0)
                                    <small class="text-muted mt-2 mb-2">{{count($active_tasks)}} active task(s) in {{$department_count}} department(s)</small>
                                @endif
                            </div>
                        </div>
                    </a>

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {

    });
</script>
@endpush