@extends('admin.main')
@section('page_title', 'Edit Roles for Package')
@section('title', 'Edit Roles for Package: ' . $package->name)

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ rrt_route($controllerName.'/storeRoles', ['id'=>$package->id??""]) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>{{__('Role Name')}}</th>
                                <th>{{__('Usage Count')}}</th>
                                @if($aiId==1)
                                <th>{{__('Days Available for Download')}}</th>
                                @endif
                                <th>{{__('Price')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="roles[{{ $role->id }}][enabled]"
                                                   value="1" {{ $existingRoles->contains($role->id) ? 'checked' : '' }}>
                                            {{ $role->name }}
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number" name="roles[{{ $role->id }}][usage_count]"
                                               value="{{ $existingRoles->where('id', $role->id)->first()->pivot->usage_count ?? 0 }}"
                                               class="form-control" min="0">
                                    </td>
                                    @if($aiId==1)
                                    <td>
                                        <input type="number" name="roles[{{ $role->id }}][download_available]"
                                               value="{{ $existingRoles->where('id', $role->id)->first()->pivot->download_available ?? 0 }}"
                                               class="form-control" min="0">
                                    </td>
                                    @endif
                                    <td>
                                        <input type="number" step="0.01" name="roles[{{ $role->id }}][price]"
                                               value="{{ $existingRoles->where('id', $role->id)->first()->pivot->price ?? 0 }}"
                                               class="form-control" min="0">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{__('Save Roles')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
