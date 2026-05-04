@extends($pathViewController . '.form', [
    'title' => $title ?? "",
    'code' => $code??"",
    'type' => $type??"",
    'next' => rrt_route($controllerName . '/pricing', ['code' => $code]),
    'prev' => rrt_route($controllerName . '/metadata', ['code' => $code]),
])
@section('content_title', __('Collaborators'))
@section('content_step', '4')
@section('content_form')
    <div class="row">
        <div class="col-md-12">
            <div class="border border-secondary p-3 d-flex align-items-center justify-content-between collaborator-info">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('studio/images/user.jpg') }}" class="rounded-circle mr-3 h-20 w-20">
                    <div>
                        <i class="fa fa-user"></i>
                        <span>{{ __('Collaborator') }}</span>
                        <h4 class="mb-0">{{ __('You') }}</h4>
                    </div>
                </div>
                <div class="mr-3">
                    <i class="fa fa-user"></i>
                    <span>{{ __('Role') }}</span>
                    <h4>{{ __('Main Collaborator') }}</h4>
                </div>
                <div class="mr-3">
                    <i class="fa fa-user"></i>
                    <span>{{ __('Profit Share') }}</span>
                    <h4>100%</h4>
                </div>
                <div>
                    <i class="fa fa-user"></i>
                    <span>{{ __('Publishing %') }}</span>
                    <h4>100%</h4>
                </div>
            </div>
            <div class="content-collaborator">
            </div>
            {{-- <div class="mt-3">
                <button type="button" class="btn btn-light text-primary btn-rounded" id="btnAddCollaborators">
                    <span class="fa fa-plus"></span>
                    <span>{{ __('Add collaborator') }}</span>
                </button>
            </div> --}}
        </div>

    </div>
@endsection
@push('script')
    <script>
        const btnAddCollaborators = $("#btnAddCollaborators");
        const contentCollaborator = $(".content-collaborator");
        let number = 1;
        btnAddCollaborators.click(function() {
            let xhtml = '';
            xhtml += `@include($pathViewController . '/collaborator-item')`;
            if (number <= 5) {
                contentCollaborator.append(xhtml);

            }

            $(".select2").select2();
            deleteRow();
            number++;
        })
        const deleteRow = () => {
            const btnDeleteRow = $(".btnDeleteRow");
            btnDeleteRow.click(function() {
                const parent = $(this).closest('.content-collaborator-item');
                parent.remove();
            })
        }
    </script>
@endpush
