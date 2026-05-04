@extends('admin.main')
@section('content')
<div class="app-content content py-5">
    <div class="content-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-lock text-warning" style="font-size: 72px;"></i>
                            </div>
                            <h2 class="mb-3">Access Denied</h2>
                            <p class="mb-4">Sorry, you don't have permission to access this page. Please contact an administrator if you need assistance.</p>
                            <a href="{{ rrt_route('admin/home/index') }}" class="btn btn-primary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 