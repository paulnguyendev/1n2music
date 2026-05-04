@extends($pathViewController . '.form', [
    'code' => $code,
    'type' => $type,
    'title' => $title,
    'next' =>(isset($type) && $type == "track") ? rrt_route($controllerName . '/pricing', ['code' => $code,'type' => $type]) : rrt_route($controllerName . '/review', ['code' => $code,'type' => $type]),
    'prev' =>rrt_route($controllerName . '/basicInfo', ['code' => $code,'type' => $type]),
])
@section('content_title', __('Meta Data'))
@section('content_step', '3')
@section('content_form')
    <div class="row">
        <div class="col-md-12 mb-3">
            <h4 class="mb-3">{{__('Track details')}}</h4>
            <div class="form-group mb-4">
                <label for="">{{__('Tags (up to 3)')}}</label>
                <input type="tagsinput" class="form-control" data-role="tagsinput" name="tags">
            </div>
            <div class="form-group">
                <label for="">{{__('Genre')}}</label>
                <select name="genres[]" type="select" class="form-control select2" multiple>
                    @if ($genres)
                        @foreach ($genres as $genre)
                            <option value="{{ $genre['id'] ?? '' }}"> {{ $genre['name'] ?? '' }}</option>
                        @endforeach
                    @endif


                </select>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <h4 class="mb-3">{{__('Mood')}}</h4>
            <div class="form-group">
                <label for="">{{__('Moods')}}</label>
                <select name="moods[]" type="select" class="form-control select2" multiple>
                    @if ($moods)
                        @foreach ($moods as $mood)
                            <option value="{{ $mood['id'] ?? '' }}"> {{ $mood['name'] ?? '' }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <h4 class="mb-3">{{__('BPM')}}</h4>
            <div class="row">
{{--                <div class="col-md-4">--}}
{{--                    <div class="form-group">--}}
{{--                        <label for="">Key</label>--}}
{{--                        <select name="track_key_id" type="select" class="form-control select2">--}}
{{--                            <option value="A_FLAT_MINOR"> A♭m</option>--}}
{{--                            <option value="A_FLAT_MAJOR"> A♭M--}}
{{--                            </option>--}}
{{--                            <option value="A_MINOR"> Am--}}
{{--                            </option>--}}
{{--                            <option value="A_MAJOR"> AM--}}
{{--                            </option>--}}
{{--                            <option value="A_SHARP_MINOR"> A♯m--}}
{{--                            </option>--}}
{{--                            <option value="A_SHARP_MAJOR"> A♯M--}}
{{--                            </option>--}}
{{--                            <option value="B_FLAT_MINOR"> B♭m--}}
{{--                            </option>--}}
{{--                            <option value="B_FLAT_MAJOR"> B♭M--}}
{{--                            </option>--}}
{{--                            <option value="B_MINOR"> Bm--}}
{{--                            </option>--}}
{{--                            <option value="B_MAJOR"> BM--}}
{{--                            </option>--}}
{{--                            <option value="C_FLAT_MAJOR"> C♭M--}}
{{--                            </option>--}}
{{--                            <option value="C_MINOR"> Cm--}}
{{--                            </option>--}}
{{--                            <option value="C_MAJOR"> CM--}}
{{--                            </option>--}}
{{--                            <option value="C_SHARP_MINOR"> C♯m--}}
{{--                            </option>--}}
{{--                            <option value="C_SHARP_MAJOR"> C♯M--}}
{{--                            </option>--}}
{{--                            <option value="D_FLAT_MINOR"> D♭m--}}
{{--                            </option>--}}
{{--                            <option value="D_FLAT_MAJOR"> D♭M--}}
{{--                            </option>--}}
{{--                            <option value="D_MINOR"> Dm--}}
{{--                            </option>--}}
{{--                            <option value="D_MAJOR"> DM--}}
{{--                            </option>--}}
{{--                            <option value="D_SHARP_MINOR"> D♯m--}}
{{--                            </option>--}}
{{--                            <option value="D_SHARP_MAJOR"> D♯M--}}
{{--                            </option>--}}
{{--                            <option value="E_FLAT_MINOR"> E♭m--}}
{{--                            </option>--}}
{{--                            <option value="E_FLAT_MAJOR"> E♭M--}}
{{--                            </option>--}}
{{--                            <option value="E_MINOR"> Em--}}
{{--                            </option>--}}
{{--                            <option value="E_MAJOR"> EM--}}
{{--                            </option>--}}
{{--                            <option value="F_MINOR"> Fm--}}
{{--                            </option>--}}
{{--                            <option value="F_MAJOR"> FM--}}
{{--                            </option>--}}
{{--                            <option value="F_SHARP_MINOR"> F♯m--}}
{{--                            </option>--}}
{{--                            <option value="F_SHARP_MAJOR"> F♯M--}}
{{--                            </option>--}}
{{--                            <option value="G_FLAT_MAJOR"> G♭M--}}
{{--                            </option>--}}
{{--                            <option value="G_MINOR"> Gm--}}
{{--                            </option>--}}
{{--                            <option value="G_MAJOR"> GM--}}
{{--                            </option>--}}
{{--                            <option value="G_SHARP_MINOR"> G♯m--}}
{{--                            </option>--}}
{{--                            <option value="G_SHARP_MAJOR"> G♯M--}}
{{--                            </option>--}}
{{--                            <option value="NONE"> None--}}
{{--                            </option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="col-md-4">
                    <label for="">{{__('BPM')}}</label>
                    <input type="number" class="form-control" name="bpm_number">
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <h4 class="mb-3">{{__('Instruments')}}</h4>
            <div class="form-group">
                <label for="">{{__('Instruments')}}</label>
                <select name="invs[]" type="select" class="form-control select2" multiple>
                    @if ($invs)
                        @foreach ($invs as $inv)
                            <option value="{{ $inv['id'] ?? '' }}"> {{ $inv['name'] ?? '' }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(`.form-control[data-role='tagsinput']`).tagsinput({
            tagClass: 'badge bg-info',
            maxTags: 3
        });
    </script>
@endpush
