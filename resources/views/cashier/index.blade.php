@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row" id="table-detail"></div>
        <div class="row">
            <div class="col-md-5">
                <button class="btn btn-primary btn-block" id="btn-show-table">View All Table</button>
            </div>
            <div class="col-md-5"></div>
        </div>
    </div>
    <script>
        $(document).ready(function (){
           $('#btn-show-table').click(function (){
              $.get("/cashier/gettable", function (data){
                  $('#table-detail').html(data);
              })
           });
        });
    </script>
@endsection
