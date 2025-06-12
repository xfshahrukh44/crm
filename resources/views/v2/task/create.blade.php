@extends('v2.layouts.app')

@section('title', 'Create task')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.7.0/summernote.css" rel="stylesheet">
@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brief-pg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brief-info">
                            <h2 class="mt-4">
                                Task Form <small>({{ $project->name }})</small>
                            </h2>
                            <form action="{{route('v2.tasks.store')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="project_id" value="{{$project->id}}">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Select Category *</label>
                                            <select class="form-control select2" name="category_id[]" id="category_id" multiple>
                                                <option value="">Select category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}" {{ old('category_id') == $category->id || request()->get('category_id') == $category->id ? 'selected' : ' '}}>{{$category->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Due Date *</label>
                                            <input class="form-control" type="date" name="duedate" id="duedate" value="{{old('duedate') ?? ''}}" required>
                                            @error('duedate')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Description *</label>
                                            <textarea style="height: unset;" class="form-control" name="description" id="description" cols="30" rows="4" required="required" required>{{old('description') ?? ''}}</textarea>
                                            @error('description')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Select File</label>
                                            <input type="file" id="files" multiple name="filenames[]" class="dropify">
                                            @error('filenames')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-auto">
                                    <div class="brief-bttn">
                                        <button class="btn brief-btn" type="submit">Submit Form</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.7.0/summernote.js"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
        $('#description').summernote({ height: 200 });
    </script>

    <script>
        $(document).ready(() => {

        });
    </script>
@endsection
