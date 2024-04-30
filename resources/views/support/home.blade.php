
@extends('layouts.app-support')
@section('content')

<div class="breadcrumb">
    <h1 class="mr-2">Dashboard</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6">
        
        <a href="{{ route('support.project') }}">
            <div class="card card-icon mb-4">
                <div class="card-body text-center"><i class="i-Suitcase"></i>
                    <p class="text-muted mt-2 mb-2">Projects</p>
                    <p class="text-primary text-24 line-height-1 m-0">{{ $project_count }}</p>
                </div>
            </div>
        </a>
        
    </div>
</div>
@endsection
