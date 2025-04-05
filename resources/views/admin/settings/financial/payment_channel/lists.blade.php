<div class="tab-pane fade @if(request()->get('tab') == "payment_channels") active show @endif" id="payment_channels" role="tabpanel" aria-labelledby="payment_channels-tab">
    <div class="card">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped font-14">
                    <tr>
                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                        <th>{{ trans('public.status') }}</th>
                        <th>{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach($paymentChannels as $paymentChannel)
                        <tr>
                            <td class="text-left">{{ $paymentChannel->title }}</td>
                            <td>
                                @if($paymentChannel->status == 'active')
                                    <span class="text-success">{{ trans('admin/main.active') }}</span>
                                @else
                                    <span class="text-danger">{{ trans('admin/main.inactive') }}</span>
                                @endif
                            </td>

                            <td>
                                @can('admin_payment_channel_edit')
                                    <a href="{{ getAdminPanelUrl() }}/settings/payment_channels/{{ $paymentChannel->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin_payment_channel_toggle_status')
                                    <a href="{{ getAdminPanelUrl() }}/settings/payment_channels/{{ $paymentChannel->id }}/toggleStatus" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.'.(($paymentChannel->status == 'active') ? 'inactive' : 'active')) }}">
                                        @if($paymentChannel->status == 'inactive')
                                            <i class="fa fa-arrow-up"></i>
                                        @else
                                            <i class="fa fa-arrow-down"></i>
                                        @endif
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            {{ $paymentChannels->appends(['tab' => "payment_channels"])->links() }}
        </div>

    </div>
</div>
