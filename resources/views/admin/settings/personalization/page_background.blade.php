<div class=" mt-3">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="name" value="page_background">
                <input type="hidden" name="page" value="personalization">

                @php
                    $pages = ['admin_login','admin_dashboard', 'login','register','remember_pass','verification',
                        'search', 'tags', 'categories','become_instructor','certificate_validation','blog','instructors',
                        'organizations','dashboard','user_cover','instructor_finder_wizard','products_lists', 'upcoming_courses_lists', 'user_default_signature'
                    ];
                @endphp

                @foreach($pages as $page)
                    <div class="form-group">
                        <label class="input-label">{{ trans('admin/main.'.$page.'_background') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="button" class="input-group-text admin-file-manager" data-input="{{ $page }}" data-preview="holder">
                                    <i class="fa fa-chevron-up"></i>
                                </button>
                            </div>
                            <input type="text" name="value[{{ $page }}]" id="{{ $page }}" value="{{ (!empty($itemValue) and !empty($itemValue[$page])) ? $itemValue[$page] : old($page) }}" class="form-control"/>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
