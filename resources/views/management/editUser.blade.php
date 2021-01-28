@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('management.inc.sidebar')
            <div class="col-md-8">
                <i class="fas fa-user"></i> Edit User
                <hr>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="/management/user/{{$user->id}}" method="POST">

                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="userName">User Name</label>
                        <input type="text" name="name" value="{{$user->name}}" class="form-control" placeholder="Name...">
                    </div>
                    <div class="form-group">
                        <label for="userEmail">Email</label>
                        <input type="text" name="email" class="form-control" value="{{$user->email}}" placeholder="Email...">
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password...">
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Role</label>
                        <select name="role" class="form-control">
                            <option value="admin" {{$user->role == 'admin' ? 'selected':''}}>Admin</option>
                            <option value="cashier" {{$user->role == 'cashier' ? 'selected':''}}>Cashier</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection

