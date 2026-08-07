@extends('admin.components.main')

@section('main-content')
<div class="pc-container">
    <div class="pc-content">
        <form id="form-meta" action="{{route('short.editmeta', $shortlink->id)}}" method="POST">
            @csrf
            <textarea name="meta" type="text" class="form-control" style="height: 200px">{{ $shortlink->meta }}</textarea>
            <button type="submit" class="btn btn-lg btn-primary rounded-1 mt-4 float-end">Simpan</button>
        </form>
    </div>
</div>
@endsection