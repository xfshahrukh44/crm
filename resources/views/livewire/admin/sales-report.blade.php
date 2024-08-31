<div>
    @include('livewire.loader')
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

        /*.invoices_wrapper::-webkit-scrollbar {*/
        /*    display: none;*/
        /*}*/
        /*.invoices_wrapper {*/
        /*    -ms-overflow-style: none;  !* IE and Edge *!*/
        /*    scrollbar-width: none;  !* Firefox *!*/
        /*}*/
    </style>

    <div class="breadcrumb">
        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="mr-2">Sales report</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            {{--brand detail--}}
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="row mb-4">
                        <div class="col-md-8 offset-md-2  form-group">
                            <label for="sale_agent_id">Select business unit head</label>
                            <select name="" class="form-control" id="buh_id" wire:model="admin_sales_report_buh_id">
                                <option value="">Select business unit head</option>
                                @foreach($buh_users as $buh_user)
                                    <option value="{{$buh_user->id}}">{{$buh_user->name . ' ' . $buh_user->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if(!is_null($report))
                        <div class="row mb-4">
                            <div class="col-md-8 offset-md-2 my-2 py-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr class="text-center" style="background: #F3F3F3;">
                                            <th colspan="999"><h4>Today</h4></th>
                                        </tr>
                                        <tr class="text-center">
                                            {{--<tr>--}}
                                            {{--<th>Today</th>--}}
                                            @forelse($report['today'] as $symbol => $total)
                                                <th colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="col-md-8 offset-md-2 my-2 py-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr class="text-center" style="background: #F3F3F3;">
                                            <th colspan="999"><h4>This week</h4></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Monday</th>
                                            @forelse($report['this_week']['Monday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">Tuesday</th>
                                            @forelse($report['this_week']['Tuesday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">Wednesday</th>
                                            @forelse($report['this_week']['Wednesday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">Thursday</th>
                                            @forelse($report['this_week']['Thursday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">Friday</th>
                                            @forelse($report['this_week']['Friday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">Saturday</th>
                                            @forelse($report['this_week']['Saturday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">Sunday</th>
                                            @forelse($report['this_week']['Sunday'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th colspan="999" class="text-center">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr style="background: #F3F3F3;">
                                            <th class="text-center">Total</th>
                                            @forelse($report['this_week_total'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="col-md-8 offset-md-2 my-2 py-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr class="text-center" style="background: #F3F3F3;">
                                        <th colspan="999"><h4>This month</h4></th>
                                    </tr>
                                    <tr>
                                        {{--<tr>--}}
                                        {{--<th>Today</th>--}}
                                        @forelse($report['this_month'] as $symbol => $total)
                                            <th class="text-center">
                                                <strong>{{$symbol}}</strong>
                                                <span>{{number_format($total, 0)}}</span>
                                            </th>
                                        @empty
                                            <th class="text-center" colspan="999">
                                                <strong>-</strong>
                                            </th>
                                        @endforelse
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="col-md-8 offset-md-2 my-2 py-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr class="text-center" style="background: #F3F3F3;">
                                            <th colspan="999"><h4>This year</h4></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">January</th>
                                            @forelse($report['this_year']['January'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">February</th>
                                            @forelse($report['this_year']['February'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">March</th>
                                            @forelse($report['this_year']['March'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">April</th>
                                            @forelse($report['this_year']['April'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">May</th>
                                            @forelse($report['this_year']['May'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">June</th>
                                            @forelse($report['this_year']['June'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">July</th>
                                            @forelse($report['this_year']['July'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">August</th>
                                            @forelse($report['this_year']['August'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">September</th>
                                            @forelse($report['this_year']['September'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">October</th>
                                            @forelse($report['this_year']['October'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">November</th>
                                            @forelse($report['this_year']['November'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr>
                                            <th class="text-center">December</th>
                                            @forelse($report['this_year']['December'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                        <tr style="background: #F3F3F3;">
                                            <th class="text-center">Total</th>
                                            @forelse($report['this_year_total'] as $symbol => $total)
                                                <th class="text-center" colspan="{{$loop->last ? '999' : ''}}">
                                                    <strong>{{$symbol}}</strong>
                                                    <span>{{number_format($total, 0)}}</span>
                                                </th>
                                            @empty
                                                <th class="text-center" colspan="999">
                                                    <strong>-</strong>
                                                </th>
                                            @endforelse
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>

    </script>
</div>