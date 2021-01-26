@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row" id="table-detail"></div>
        <div class="row">
            <div class="col-md-5">
                <button class="btn btn-primary btn-block" id="btn-show-table">View All Table</button>
                <div id="selected-table"></div>
                <div id="order-details"></div>
            </div>
            <div class="col-md-7">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach($categories as $category)
                            <a class="nav-item nav-link" data-id="{{$category->id}}" data-toggle="tab">
                                {{$category->name}}
                            </a>
                        @endforeach
                    </div>
                </nav>
                <div id="list-menu" class="row mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="totalAmount"></h3>
                    <h3 class="changedAmount"></h3>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" id="recieved-amount" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="payment">Payment Type</label>
                        <select class="form-control" id="payment-type">
                            <option value="cash">Cash</option>
                            <option value="credit card">Credit Card</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save-payment" disabled >Save Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function (){
            // make table hidden by default
            $('#table-detail').hide();

            // show all tables when button clicked
           $('#btn-show-table').click(function (){
               if ($('#table-detail').is(":hidden")) {
                  $.get("/cashier/gettable", function (data){
                      $('#table-detail').html(data);
                      $('#table-detail').slideDown('fast');
                      $('#btn-show-table').html('Hide Tables').removeClass('btn-primary').addClass('btn-danger');
                  })
               } else {
                   $('#table-detail').slideUp('fast');
                   $('#btn-show-table').html('View All Tables').removeClass('btn-danger').addClass('btn-primary');
               }
           });

           //load menus by category AJax
            $('.nav-link').click(function () {
                $.get("/cashier/getMenuByCategory/"+$(this).data('id'), function (data) {
                    $('#list-menu').hide();
                    $('#list-menu').html(data);
                    $('#list-menu').fadeIn('fast');
                })
            })

            // detect button table onclick
            var SELECTED_TABLE_ID = "";
            var SELECTED_TABLE_NAME = "";
            var SALE_ID = "";

            $('#table-detail').on("click", ".btn-table", function () {
                SELECTED_TABLE_ID = $(this).data("id");
                SELECTED_TABLE_NAME = $(this).data("name");

                $('#selected-table').html('<br><h3>Table: '+SELECTED_TABLE_NAME+'</h3><hr>')
                $.get("/cashier/getSaleDetailsByTable/"+SELECTED_TABLE_ID, function (data) {
                    $('#order-details').html(data)
                })
            })

            $('#list-menu').on("click", ".btn-menu", function () {
                if (SELECTED_TABLE_ID == "") {
                    alert("You need to select a table for the customer first")
                } else {
                    var menuId = $(this).data("id")
                    $.ajax({
                        type: "POST",
                        data: {
                            "_token" : $('meta[name="csrf-token"]').attr('content'),
                            "menu_id" : menuId,
                            "table_id" : SELECTED_TABLE_ID,
                            "table_name" : SELECTED_TABLE_NAME,
                            "quantity" : 1
                        },
                        url: "/cashier/orderFood",
                        success: function (data) {
                            $('#order-details').html(data)
                        }
                    })
                }

            })

            $('#order-details').on("click", ".btn-confirm-order", function () {
                var saleId = $(this).data("id")

                $.ajax({
                    type: "POST",
                    data: {
                        "_token" : $('meta[name="csrf-token"]').attr('content'),
                        "sale_id" : saleId
                    },
                    url: "/cashier/confirmOrderStatus",
                    success: function (data) {
                        $('#order-details').html(data)
                    }
                })
            })

            // delete sale detail
            $('#order-details').on("click", ".btn-delete-saledetail", function () {
                var saleDetailId = $(this).data("id")
                $.ajax({
                    type: "POST",
                    data: {
                        "_token" : $('meta[name="csrf-token"]').attr('content'),
                        "saleDetail_id" : saleDetailId
                    },
                    url: "/cashier/deleteSaleDetail",
                    success: function (data) {
                        $('#order-details').html(data);
                    }
                })
            })

            // when user click on payment button
            $('#order-details').on("click", ".btn-payment", function () {
                var totalAmount = $(this).attr('data-totalAmount');
                $('.totalAmount').html("Total Amount : " + totalAmount)
                $('#recieved-amount').val('')
                $('.changedAmount').html('')
                SALE_ID = $(this).data("id")
            })

            //calculate change when payment
            $('#recieved-amount').keyup(function (){
                var totalAmount = $('.btn-payment').attr('data-totalAmount');
                var recievedAmount = $(this).val();
                var changedAmount = recievedAmount - totalAmount;

                $('.changedAmount').html("Total Change : " + changedAmount)

                // check if change amount is bigger then total amount then enable save payment button
                if (changedAmount >= 0) {
                    $('.btn-save-payment').prop('disabled', false)
                } else {
                    $('.btn-save-payment').prop('disabled', true)
                }
            })

            // save payment functionality
            $('.btn-save-payment').click(function () {
                var recievedAmount = $('#recieved-amount').val()
                var paymentType = $('#payment-type').val()
                var saleId = SALE_ID;

                $.ajax({
                    type: "POST",
                    data: {
                        "_token" : $('meta[name="csrf-token"]').attr('content'),
                        "saleID" : saleId,
                        "recievedAmount" : recievedAmount,
                        "paymentType" : paymentType
                    },
                    url: "/cashier/savePayment",
                    success: function (data) {
                        window.location.href = data;
                    }
                })
            })

        });
    </script>
@endsection
