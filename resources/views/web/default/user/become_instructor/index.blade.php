@extends(getTemplate().'.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <div class="container">

        <div class="row login-container">
            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('become_instructor') }}" class="img-cover" alt="Login">
            </div>

            <div class="col-12 col-md-6">
                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('site.become_instructor') }}</h1>

                    <form method="Post" action="/become-instructor" class="mt-35">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="js-instructor-label font-weight-500 text-dark-blue {{ !$isInstructorRole ? 'd-none' : '' }}">{{ trans('update.instructor_occupations') }}</label>
                            <label class="js-organization-label font-weight-500 text-dark-blue {{ !$isOrganizationRole ? 'd-none' : '' }}">{{ trans('update.organization_occupations') }}</label>

                            <div class="d-flex flex-wrap mt-5">

                                @foreach($categories as $category)
                                    @if(!empty($category->subCategories) and count($category->subCategories))
                                        @foreach($category->subCategories as $subCategory)
                                            <div class="checkbox-button mr-15 mt-10 font-14">
                                                <input type="checkbox" name="occupations[]" id="checkbox{{ $subCategory->id }}" value="{{ $subCategory->id }}" @if(!empty($occupations) and in_array($subCategory->id,$occupations)) checked="checked" @endif>
                                                <label for="checkbox{{ $subCategory->id }}">{{ $subCategory->title }}</label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="checkbox-button mr-15 mt-10 font-14">
                                            <input type="checkbox" name="occupations[]" id="checkbox{{ $category->id }}" value="{{ $category->id }}" @if(!empty($occupations) and in_array($category->id,$occupations)) checked="checked" @endif>
                                            <label for="checkbox{{ $category->id }}">{{ $category->title }}</label>
                                        </div>
                                    @endif
                                @endforeach

                                @if($errors->has('occupations'))
                                    <div class="text-danger font-14">{{ $errors->first('occupations') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="js-instructor-label font-weight-500 text-dark-blue {{ !$isInstructorRole ? 'd-none' : '' }}">{{ trans('update.instructor_select_role') }}</label>
                            <label class="js-organization-label font-weight-500 text-dark-blue {{ !$isOrganizationRole ? 'd-none' : '' }}">{{ trans('update.organization_select_role') }}</label>

                            <select name="role" class="form-control @error('role')  is-invalid @enderror">
                                <option selected disabled>{{ trans('update.select_role') }}</option>

                                <option value="{{ \App\Models\Role::$teacher }}" {{ $isInstructorRole ? 'selected' : '' }}>{{ trans('panel.teacher') }}</option>
                                <option value="{{ \App\Models\Role::$organization }}" {{ $isOrganizationRole ? 'selected' : '' }}>{{ trans('home.organization') }}</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="js-instructor-label font-weight-500 text-dark-blue {{ !$isInstructorRole ? 'd-none' : '' }}">{{ trans('update.instructor_certificate_and_documents') }}</label>
                            <label class="js-organization-label font-weight-500 text-dark-blue {{ !$isOrganizationRole ? 'd-none' : '' }}">{{ trans('update.organization_certificate_and_documents') }}</label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="button" class="input-group-text panel-file-manager" data-input="certificate" data-preview="holder">
                                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                                    </button>
                                </div>
                                <input type="text" name="certificate" id="certificate" value="{{ !empty($lastRequest) ? $lastRequest->certificate : old('certificate') }}" class="form-control "/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="js-instructor-label font-weight-500 text-dark-blue {{ !$isInstructorRole ? 'd-none' : '' }}">{{ trans('update.instructor_select_account_type') }}</label>
                            <label class="js-organization-label font-weight-500 text-dark-blue {{ !$isOrganizationRole ? 'd-none' : '' }}">{{ trans('update.organization_select_account_type') }}</label>
                            <select name="bank_id" class="js-user-bank-input form-control @error('bank_id')  is-invalid @enderror">
                                <option selected disabled>{{ trans('financial.select_account_type') }}</option>

                                @foreach($userBanks as $userBank)
                                    <option value="{{ $userBank->id }}" @if(!empty($user) and !empty($user->selectedBank) and $user->selectedBank->user_bank_id == $userBank->id) selected="selected" @endif data-specifications="{{ json_encode($userBank->specifications->pluck('name','id')->toArray()) }}">{{ $userBank->title }}</option>
                                @endforeach
                            </select>
                            @error('bank_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="js-bank-specifications-card">
                            @if(!empty($user) and !empty($user->selectedBank) and !empty($user->selectedBank->bank))
                                @foreach($user->selectedBank->bank->specifications as $specification)
                                    @php
                                        $selectedBankSpecification = $user->selectedBank->specifications->where('user_selected_bank_id', $user->selectedBank->id)->where('user_bank_specification_id', $specification->id)->first();
                                    @endphp
                                    <div class="form-group">
                                        <label class="font-weight-500 text-dark-blue">{{ $specification->name }}</label>
                                        <input type="text" name="bank_specifications[{{ $specification->id }}]" value="{{ (!empty($selectedBankSpecification)) ? $selectedBankSpecification->value : '' }}" class="form-control"/>
                                    </div>
                                @endforeach
                            @endif
                        </div>


                        <div class="form-group">
                            <label class="js-instructor-label font-weight-500 text-dark-blue {{ !$isInstructorRole ? 'd-none' : '' }}">{{ trans('update.instructor_identity_scan') }}</label>
                            <label class="js-organization-label font-weight-500 text-dark-blue {{ !$isOrganizationRole ? 'd-none' : '' }}">{{ trans('update.organization_identity_scan') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="button" class="input-group-text panel-file-manager" data-input="identity_scan" data-preview="holder">
                                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                                    </button>
                                </div>
                                <input type="text" name="identity_scan" id="identity_scan" value="{{ (!empty($user)) ? $user->identity_scan : old('identity_scan') }}" class="form-control @error('identity_scan')  is-invalid @enderror"/>
                                @error('identity_scan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="js-instructor-label font-weight-500 text-dark-blue {{ !$isInstructorRole ? 'd-none' : '' }}">{{ trans('update.instructor_extra_information') }}</label>
                            <label class="js-organization-label font-weight-500 text-dark-blue {{ !$isOrganizationRole ? 'd-none' : '' }}">{{ trans('update.organization_extra_information') }}</label>
                            <textarea name="description" rows="6" class="form-control">{{ !empty($lastRequest) ? $lastRequest->description : old('description') }}</textarea>
                        </div>

                        <div class="js-form-fields-card">
                            @if(!empty($formFields))
                                {!! $formFields !!}
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ (!empty(getRegistrationPackagesGeneralSettings('show_packages_during_registration')) and getRegistrationPackagesGeneralSettings('show_packages_during_registration')) ? trans('webinars.next') : trans('site.send_request') }}</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/js/parts/forms.min.js"></script>
    <script src="/assets/default/js/parts/become_instructor.min.js"></script>
@endpush
