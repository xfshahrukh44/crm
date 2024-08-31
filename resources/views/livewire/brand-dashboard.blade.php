<div>
    @include('livewire.loader')
    <style>
        img {
            height: 50px;
            object-fit: contain;
        }

        .card-body.text-center {
            min-height: 160px;
        }

        p.text-muted.mt-2.mb-2 {
            font-size: 15px;
        }

        .card-body.text-center:hover {
            box-shadow: 0px 0px 15px 8px #00aeee1a;
        }

        .brand-card:hover {
            cursor: pointer !important;
        }
    </style>

    <div class="breadcrumb">
        <h1 class="mr-2">Brands dashboard</h1>
    </div>

    @if(auth()->user()->is_employee == 2)
        <div class="breadcrumb">
            <h5>
                <a href="#" wire:click="set_active_page('admin_sales_report')" class="badge badge-success">View sales report</a>
            </h5>
        </div>
    @endif
    @if(auth()->user()->is_employee == 6)
        <div class="breadcrumb">
            <h5>
                <a href="#" wire:click="set_active_page('manager_sales_report')" class="badge badge-success">View sales report</a>
            </h5>
        </div>
    @endif
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card text-left">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="package">Search brand</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" wire:model.debounce.500ms="brand_name" placeholder="Brand information">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <!-- CARD ICON-->
            <div class="row">
                @foreach($brands as $brand)
                    <div class="col-lg-2 col-md-6 col-sm-6">

                        <a wire:click="set_active_page('brands_detail-{{$brand->id}}')">
                            <div class="card card-icon mb-4 brand-card" style="height: 180px;">
                                <div class="card-body text-center">
                                    <div class="preview">
                                        <img style="" src="{{asset($brand->logo)}}" alt="">
                                    </div>
                                    <p class="text-muted mt-2 mb-2">{{$brand->name}}</p>
                                    <small class="text-muted mt-2 mb-2">Clients: {{count($brand->clients)}} | Projects: {{count($brand->projects)}}</small>
                                    {{--                                <p class="text-primary text-24 line-height-1 m-0">{{$brand->name}}</p>--}}
                                </div>
                            </div>
                        </a>

                    </div>
                @endforeach
            </div>
            {{ $brands->links() }}
        </div>
    </div>
</div>