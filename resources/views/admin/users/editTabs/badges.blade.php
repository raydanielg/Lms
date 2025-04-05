<div class="tab-pane mt-3 fade" id="badges" role="tabpanel" aria-labelledby="badges-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/users/{{ $user->id .'/badgesUpdate' }}" method="Post">
                {{ csrf_field() }}

                <div class="form-group">
                    <select name="badge_id" class="form-control @error('badge_id') is-invalid @enderror">
                        <option value="">{{ trans('admin/main.select_badge') }}</option>

                        @foreach($badges as $badge)
                            <option value="{{ $badge->id }}">{{ $badge->title }}</option>
                        @endforeach
                    </select>
                    @error('badge_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>

        </div>

        <div class="col-12">
            <div class="mt-5">
                <h5>{{ trans('admin/main.custom_badges') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('admin/main.title') }}</th>
                            <th>{{ trans('admin/main.image') }}</th>
                            <th>{{ trans('admin/main.condition') }}</th>
                            <th>{{ trans('admin/main.description') }}</th>
                            <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                            <th>{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($user->customBadges))
                            @foreach($user->customBadges as $customBadge)

                                @php
                                    $condition = json_decode($customBadge->badge->condition);
                                @endphp

                                <tr>
                                    <td>{{ $customBadge->badge->title }}</td>
                                    <td>
                                        <img src="{{ $customBadge->badge->image }}" width="24"/>
                                    </td>
                                    <td>{{ $condition->from }} to {{ $condition->to }}</td>
                                    <td width="25%">
                                        <p>{{ $customBadge->badge->description  }}</p>
                                    </td>
                                    <td class="text-center">{{ dateTimeFormat($customBadge->badge->created_at,'j M Y') }}</td>
                                    <td>
                                        @can('admin_users_edit')
                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/users/'.$user->id.'/deleteBadge/'.$customBadge->id , 'btnClass' => 'btn-sm'])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="mt-5">
                <h5>{{ trans('admin/main.auto_badges') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('admin/main.title') }}</th>
                            <th>{{ trans('admin/main.image') }}</th>
                            <th>{{ trans('admin/main.condition') }}</th>
                            <th>{{ trans('admin/main.description') }}</th>
                            <th>{{ trans('admin/main.created_at') }}</th>
                        </tr>

                        @if(!empty($userBadges))
                            @foreach($userBadges as $badge)
                                @php
                                    $badgeCondition = json_decode($badge->condition);
                                @endphp

                                <tr>
                                    <td>{{ $badge->title }}</td>
                                    <td>
                                        <img src="{{ $badge->image }}" width="24"/>
                                    </td>
                                    <td>{{ $badgeCondition->from }} to {{ $badgeCondition->to }}</td>
                                    <td width="25%">
                                        <p>{{ $badge->description  }}</p>
                                    </td>
                                    <td>{{ dateTimeFormat($badge->created_at,'j M Y') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
