@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-2">
                <div class="page-header">
                    <avatar-form :user="{{ $profileUser }}"></avatar-form>

                    <!-- <h1>
                        {{ $profileUser->name }}
                    </h1> -->

                    <!-- @can('update', $profileUser)
                    <form action="{{ route('avatar', $profileUser) }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="file" name="avatar">

                        <button type="submit" class="btn btn-primary">Add Avatar</button>
                    </form>
                    @endcan -->
<!-- 
                    <img src="/{{ $profileUser->avatar_path }}" width="200" height="200"> -->
                </div>

                @forelse($activities as $date => $activity)
                    <h3 class="page-header">{{ $date }}</h3>

                    @foreach($activity as $record)
                        @if(view()->exists("profiles.activities.{$record->type}"))
                            @include("profiles.activities.{$record->type}",['activity'  => $record])
                        @endif
                    @endforeach
                @empty
                    <p>已经没有数据了</p>
                @endforelse 
            </div>
        </div>
    </div>
@endsection