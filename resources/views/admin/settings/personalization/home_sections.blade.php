@push('styles_top')
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

<div class=" mt-3 ">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <form action="{{ getAdminPanelUrl() }}/settings/personalization/home_sections" method="post">
                                {{ csrf_field() }}
                                <select name="name" class="form-control @error('name') is-invalid @enderror">
                                    <option value="">{{ trans('admin/main.select') }}</option>

                                    @foreach(\App\Models\HomeSection::$names as $sectionName)
                                        @if(!in_array($sectionName,$selectedSectionsName))
                                            <option value="{{ $sectionName }}">{{ trans('admin/main.'.$sectionName) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <button type="submit" class="btn btn-success mt-2">{{ trans('admin/main.add_new') }}</button>
                            </form>

                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-md-6">
                            <h3 class="font-20 font-weight-bold">{{ trans('admin/main.home_sections') }}</h3>

                            <ul class="draggable-lists list-group" data-order-table="home_sections">

                                @foreach($sections as $section)
                                    <li data-id="{{ $section->id }}" class="form-group list-group">
                                        <div class="d-flex align-items-center justify-content-between p-2 border rounded-lg">
                                            <span>{{ trans('admin/main.'.$section->name) }}</span>

                                            <div class="d-flex align-items-center">
                                                @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/settings/personalization/home_sections/'. $section->id .'/delete','btnClass' => 'text-danger mr-2 font-16'])

                                                <div class="cursor-pointer move-icon font-16 mr-1">
                                                    <i class="fa fa-arrows-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach


                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>

    <script src="/assets/default/js/admin/home_sections.min.js"></script>
@endpush
