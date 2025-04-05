<div class="card">
    <div class="card-body">
        <p class="text-muted mb-1 px-2">{{ trans('update.certificate_template_box_hint') }}</p>

        <div id="certificateTemplateContainer" class="d-flex justify-content-center">
            @if(!empty($template) and !empty($template->body))
                {!! $template->body !!}
            @else
                <div class="certificate-template-container"></div>
            @endif
        </div>
    </div>
</div>
