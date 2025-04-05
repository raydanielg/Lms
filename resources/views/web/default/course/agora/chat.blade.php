@push('styles_top')

@endpush

<div class="agora-chat d-flex flex-column h-100">
    @if(!empty($session->agora_settings) and $session->agora_settings->chat)
        <div id="chatView" class="agora-chat-box pb-30">

        </div>


        <div class="mt-15 py-15 px-15 border-top border-gray200 d-flex align-items-center ">

            <div class="flex-grow-1">
                <textarea name="message" id="messageInput" class="form-control " rows="3" placeholder="{{ trans('update.type_your_message') }}"></textarea>
            </div>


            <button type="submit" id="sendMessage" class="send-message-btn btn btn-primary p-0 rounded-circle ml-15">
                <i data-feather="send" width="18" height="18" class="text-white"></i>
            </button>
        </div>
    @else
        <div class="no-result default-no-result d-flex align-items-center justify-content-center flex-column w-100 h-100 pb-40">
            <div class="no-result-logo">
                <img src="/assets/default/img/no-results/support.png" alt="">
            </div>
            <div class="d-flex align-items-center flex-column mt-30 text-center">
                <h3 class="text-dark-blue font-16">{{ trans('update.chat_not_active') }}</h3>
                <p class="mt-5 text-center text-gray font-14">{{ trans('update.chat_not_active_hint') }}</p>
            </div>
        </div>
    @endif
</div>


@push('scripts_bottom')
    @if($session->agora_settings->chat)
        <script>
            var rtmToken = '{{ $rtmToken }}';
        </script>

        <script src="/assets/default/agora/message.min.js"></script>
    @endif
@endpush
