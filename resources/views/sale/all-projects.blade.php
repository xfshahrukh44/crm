
@extends('layouts.app-sale')
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">All Projects</h1>
</div>
<div class="separator-breadcrumb border-top"></div>


<div class="row">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">All Projects Detail</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Agent</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                            <tr>
                                <td>{{$datas->id}}</td>
                                <td>{{$datas->name}}</td>
                                <td>
                                    {{$datas->added_by->name}} {{$datas->added_by->last_name}}<br>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                        <i class="fas fa-eye mr-1"></i>
                                        View
                                    </a>
                                    <span class="content_click_to_view" hidden>
                                        {{$datas->added_by->email}}
                                    </span>
                                </td>
                                <td>
                                    {{$datas->client->name}} {{$datas->client->last_name}}<br>
                                    {{$datas->client->email}}
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm">{{$datas->brand->name}}</button>
                                </td>
                                <td>
                                    @if($datas->status == 1)
                                        <button class="btn btn-success btn-sm">Active</button>
                                    @else
                                        <button class="btn btn-danger btn-sm">Deactive</button>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('sale.form', [ 'form_id' => $datas->form_id , 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-primary btn-icon btn-sm">
                                        <span class="ul-btn__text">View Form</span>
                                    </a>
                                    @if(count($datas->tasks) != 0)
                                    <a href="{{route('sale.task.show', $datas->tasks[0]->id)}}" class="btn btn-dark btn-icon btn-sm">
                                        <span class="ul-btn__text">View Details</span>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </tfoot>
                    </table>
                    {{ $data->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $('.btn_click_to_view').on('click', function () {
                $('.btn_click_to_view').each((i, item) => {
                    $(item).prop('hidden', false);
                });

                $('.content_click_to_view').each((i, item) => {
                    $(item).prop('hidden', true);
                });

                $(this).prop('hidden', true);
                $(this).parent().find('.content_click_to_view').prop('hidden', false);
            });
        });
    </script>
@endpush
