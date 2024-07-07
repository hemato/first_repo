@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <h1>Edit Profile</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                </div>

                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control">
                    @if(auth()->user()->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile Image" class="img-thumbnail mt-2" width="100">
                    @endif
                </div>

                <div class="form-group">
                    <label for="profile_description">Profile Description</label>
                    <textarea name="profile_description" class="form-control" rows="3">{{ auth()->user()->profile_description }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
@stop
