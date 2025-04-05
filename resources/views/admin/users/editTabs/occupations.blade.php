<div class="tab-pane mt-3 fade" id="occupations" role="tabpanel" aria-labelledby="occupations-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/users/{{ $user->id .'/occupationsUpdate' }}" method="Post">
                {{ csrf_field() }}

                @foreach($categories as $category)
                    @if(!empty($category->subCategories) and count($category->subCategories))
                        @foreach($category->subCategories as $subCategory)
                            <div class="checkbox-button mr-15 mt-10">
                                <input type="checkbox" name="occupations[]" id="checkbox{{ $subCategory->id }}" value="{{ $subCategory->id }}" @if(!empty($occupations) and in_array($subCategory->id,$occupations)) checked="checked" @endif>
                                <label for="checkbox{{ $subCategory->id }}">{{ $subCategory->title }}</label>
                            </div>
                        @endforeach
                    @else
                        <div class="checkbox-button mr-15 mt-10">
                            <input type="checkbox" name="occupations[]" id="checkbox{{ $category->id }}" value="{{ $category->id }}" @if(!empty($occupations) and in_array($category->id,$occupations)) checked="checked" @endif>
                            <label for="checkbox{{ $category->id }}">{{ $category->title }}</label>
                        </div>
                    @endif
                @endforeach

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
