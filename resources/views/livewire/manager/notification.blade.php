<div>
    <style>
        .ul-widget2__username {
            font-size: 0.8rem;
        }
    </style>


    <div class="breadcrumb">
        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="mr-2">Manager notifications</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row">
        <div class="col-md-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4 class="card-title mb-3">Notification Details</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>
{{--                                        @if($notification->type == 'App\Notifications\LeadNotification')--}}
{{--                                            <a href="{{ route('admin.client.shownotification', ['client' => $notification->data['id'], 'id' => $notification->id] ) }}">{{ $notification->data['text'] }}</a></td>--}}
{{--                                        @else--}}
                                        <a href="">{{ json_decode($notification->data)->text ?? 'NA' }}</a>
{{--                                        @endif--}}
                                    </td>
                                    <td>{{ $notification->created_at ? \Carbon\Carbon::parse($notification->created_at)->diffForHumans() : 'NA' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Description</th>
                                <th>Time</th>
                            </tr>
                            </tfoot>
                        </table>
                        {{$notifications->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>