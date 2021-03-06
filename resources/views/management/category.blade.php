@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('management.inc.sidebar')
            <div class="col-md-8">
                <i class="fas fa-align-justify"></i>Category
                <a href="category/create" class="btn btn-success btn-sm float-right"><i class="fas fa-plus"></i> Create Category</a>
                <hr>
                @if(Session()->has('status'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">X</button>
                        {{ Session()->get('status') }}
                    </div>
                @endif
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Category</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td scope="row">{{ $category->id }}</td>
                                <td scope="row">{{ $category->name }}</td>
                                <td scope="row"> <a href="category/{{$category->id}}/edit" class="fas fa-edit text-primary"> Edit</a> </td>
                                <td scope="row">
                                    <form action="/management/category/{{$category->id}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link nounderline"><i class="fas fa-trash-alt text-danger"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
