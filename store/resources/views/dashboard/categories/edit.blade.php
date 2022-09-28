@extends('layouts.dashboard')

@section('title', 'Edit Categoires')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Categories</li>
    <li class="breadcrumb-item active">Edit Categories</li>
@endsection

@section('content')

    <form action="{{ route('dashboard.categories.update', $category->id) }}" method="post">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="">Category Name</label>
            <input type="text" name="name" class="form-control" value="{{$category->name}}">
        </div>

        <div class="form-group">
            <label for="">Category Parent</label>
            <select name="parent_id" class="form-control form-select">
                <option value="">Primary Category</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected($category->parent_id == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">Decsription</label>
            <textarea name="description" class="form-control">{{$category->description}}</textarea>
        </div>


        <div class="form-group">
            <label for="">Image</label>
            <input type="file" name="imge" class="form-control">
        </div>

        <div class="form-group">
            <label for="">Status</label>
            <div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" value="active" @checked($category->status =='active')>
                    <label class="form-check-label">
                        Active
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" value="archived" @checked($category->status =='archived')>
                    <label class="form-check-label">
                        Archived
                    </label>
                </div>

            </div>

        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>

    </form>


@endsection
