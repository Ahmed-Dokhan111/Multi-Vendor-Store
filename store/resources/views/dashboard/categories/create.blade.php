@extends('layouts.dashboard')

@section('title', 'Categoires')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')

    <form action="{{ route('dashboard.categories.store') }}" method="post" enctype="multipart/form-data">
        @csrf

       @include('dashboard.categories._form')

    </form>


@endsection
