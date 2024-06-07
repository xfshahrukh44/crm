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
        /* Mouse over link */
        a {text-decoration: none; color: black;}   /* Mouse over link */
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
                    <div class="col-md-8 offset-md-2">
                        <h2>{{$client->name . ' ' . $client->last_name}}</h2>
                        <p style="font-size: medium;">
                            @if($client->contact)
                                <i class="fas fa-phone text-primary"></i>
                                <a href="tel:{{$client->contact}}">{{$client->contact}}</a>
                            @endif

                            @if($client->email)
                                <i class="fas fa-envelope text-primary"></i>
                                <a href="mailto:{{$client->email}}">{{$client->email}}</a>
                            @endif
                        </p>


                        <div class="row">
                            @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) && count($client->invoices))
                                <div class="col-6">
                                    @php
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                            $route = 'admin.invoice';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $route = 'manager.invoice';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                            $route = 'sale.invoice';
                                        }
                                    @endphp
                                    <p style="font-size: medium;">
                                        <a href="{{route($route, ['client_id' => $client->id])}}">
                                            <i class="i-Credit-Card text-success"></i>
                                            View invoices
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 4]))
                                <div class="col-6">
                                    @php
                                        $client_user = \App\Models\User::where('client_id', $client->id)->first();
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                            $route = 'admin.pending.project';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $route = 'manager.pending.project';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head) {
                                            $route = 'support.pending.project';
                                        } else {
                                            $route = 'support.pending.project';
                                        }
                                    @endphp
                                    <p style="font-size: medium;">
                                        <a href="{{route($route, ['user_id' => $client_user->id])}}">
                                            <i class="i-Folder-Loading text-primary"></i>
                                            View pending projects
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>


                        @php
                            $briefs_pendings = get_briefs_pending($client->user->id);
                        @endphp
                        @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) && count($briefs_pendings))
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-center" style="border-right: 1px solid #b7b7b7;">
                                            <i class="i-Folder-Close mr-2"></i>
                                            <b>Briefs pending</b>
                                        </div>
                                        <div class="col-md-9 d-flex align-items-center" style="border-right: 1px solid #b7b7b7;">
                                            <div class="row m-auto p-2" style="font-size: 15px;">
                                                @foreach($briefs_pendings as $brief_pending)
{{--                                                    <div class="col">--}}
                                                        <span class="badge badge-pill badge-primary my-1">{{$brief_pending}}</span>
                                                        &nbsp;
{{--                                                    </div>--}}
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (\Illuminate\Support\Facades\Auth::user()->is_employee != 0)
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-center" style="border-right: 1px solid #b7b7b7;">
                                            <i class="i-Suitcase mr-2"></i>
                                            <b>Services</b>
                                        </div>
                                        <div class="col-md-9 px-0" style="border-right: 1px solid #b7b7b7;">
{{--                                            <div class="row m-auto p-2" style="font-size: 15px;">--}}
{{--                                                <div class="col-md-12">--}}
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Service</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($projects as $project)
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
                                                                <tr>
                                                                    <td>
                                                                        <a href="{{route('projects.detail', $project->id)}}">
                                                                            {{$project->name}}
                                                                        </a>
                                                                    </td>
                                                                    <th>
                                                                        @if(no_pending_tasks_left($project->id))
                                                                            <span class="badge badge-success">No pending tasks</span>
                                                                        @endif

                                                                        @if($department_count > 0)
                                                                            <small class="text-muted mt-2 mb-2">{{count($active_tasks)}} active task(s) in {{$department_count}} department(s)</small>
                                                                        @endif
                                                                    </th>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
{{--                                                </div>--}}
{{--                                            </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
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