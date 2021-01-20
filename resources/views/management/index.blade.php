@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @if(Session()->has('status'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">X</button>
                    {{ Session()->get('status') }}
                </div>
            @endif
            <div class="col-md-8">
                <div class="list-group">
                    content
                </div>
            </div>
        </div>
    </div>
@endsection
