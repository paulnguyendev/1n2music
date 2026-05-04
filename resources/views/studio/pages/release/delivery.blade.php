@extends($pathViewController . '.form')
@section('release_content')
    <div class="form-group">
        <label for="">{{__('Genre')}}</label>
        <select name="genres[]" type="select" class="form-control select2" multiple id="genres">
            @if ($genres)
                @foreach ($genres as $genre)
                    <option {{in_array($genre['id'],$itemGenres) ? "selected" : ""}} value="{{ $genre['id'] ?? '' }}"> {{ $genre['name'] ?? '' }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group">
        <label for="">{{__("Platforms")}}</label>
        <select name="shop_ids[]" type="select" class="form-control select2" multiple id="shopes">
            @if ($shopes)
                @foreach ($shopes as $shop)
                    <option {{in_array($shop['id'],$itemShopes) ? "selected" : ""}} value="{{ $shop['id'] ?? '' }}"> {{ $shop['name'] ?? '' }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group">
        <label for="">{{__('Desired Release Date')}}</label>
        <input type="date" name="release_date" class="form-control" value="{{$item['release_date'] ?? ""}}">
    </div>
    <div class="form-group">
        <label for="">{{__('2nd Desired Release Date')}}</label>
        <input type="date" name="2nd_release_date" class="form-control" value="{{$item['2nd_release_date'] ?? ""}}">
    </div>

    <div class="buttons text-right">
        <button  class="btn btn-primary"  type="submit">{{__('Next Step')}}</button>
    </div>
@endsection
