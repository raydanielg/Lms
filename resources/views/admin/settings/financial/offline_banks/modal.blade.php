<div id="addOfflineBankForm"
     data-action="{{ getAdminPanelUrl("/settings/financial/offline_banks/". (!empty($editBank) ? $editBank->id."/update" : 'store')) }}">

    @if(!empty(getGeneralSettings('content_translate')))
        <div class="form-group">
            <label class="input-label">{{ trans('auth.language') }}</label>
            <select name="locale" class="form-control {{ !empty($editBank) ? 'js-offline-banks-locale' : '' }}">
                @foreach($userLanguages as $lang => $language)
                    <option value="{{ $lang }}" @if($locale == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                @endforeach
            </select>
            @error('locale')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    @else
        <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
    @endif


    <div class="form-group">
        <label>{{ trans('admin/main.title') }}</label>
        <input type="text" name="title" value="{{ (!empty($editBank) and !empty($editBank->translate($locale))) ? $editBank->translate($locale)->title : '' }}" class="js-ajax-title form-control "/>
        <div class="invalid-feedback"></div>
    </div>

    <div class="form-group">
        <label class="input-label">{{ trans('admin/main.logo') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="input-group-text admin-file-manager" data-input="bankLogo" data-preview="holder">
                    <i class="fa fa-chevron-up"></i>
                </button>
            </div>
            <input type="text" name="logo" id="bankLogo" value="{{ (!empty($editBank)) ? $editBank->logo : '' }}" class="js-ajax-logo form-control"/>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pb-2">
        <h2 class="section-title after-line">{{ trans('update.specifications') }}</h2>

        <button type="button" class="js-add-specification btn btn-primary btn-sm ml-2">{{ trans('update.add_specification') }}</button>
    </div>

    <div class="js-specifications-lists">
        @if(!empty($editBank))
            @foreach($editBank->specifications as $specification)
                <div class="js-specification-card row align-items-center">
                    <div class="col-10">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{ trans('update.specification') }}</label>
                                    <input type="text" name="specifications[{{ $specification->id }}][name]" class="form-control" value="{{ (!empty($specification->translate($locale))) ? $specification->translate($locale)->name : '' }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{ trans('update.value') }}</label>
                                    <input type="text" name="specifications[{{ $specification->id }}][value]" class="form-control" value="{{ $specification->value }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" class="js-remove-specification btn btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>


    <div class="d-flex align-items-center justify-content-end">
        <button type="button" class="js-save-bank btn btn-primary">{{ trans('admin/main.save') }}</button>
        <button type="button" class="close-swl btn btn-danger ml-2">{{ trans('admin/main.cancel') }}</button>
    </div>
</div>
