<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CertificatesExport;
use App\Http\Controllers\Admin\traits\CertificateSettingsTrait;
use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Certificate;
use App\Models\QuizzesResult;
use App\Models\Translation\CertificateTemplateTranslation;
use App\User;
use App\Models\Quiz;
use App\Models\CertificateTemplate;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CertificateController extends Controller
{
    use CertificateSettingsTrait;

    public function index(Request $request)
    {
        $this->authorize('admin_certificate_list');

        $query = Certificate::whereNull('webinar_id');

        $query = $this->filters($query, $request);

        $certificates = $query->with(
            [
                'quiz' => function ($query) {
                    $query->with('webinar');
                },
                'student',
                'quizzesResult'
            ]
        )->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('admin/main.certificate_list_page_title'),
            'certificates' => $certificates,
            'student' => $filters['student'] ?? null,
            'instructor' => $filters['instructor'] ?? null,
            'quiz_title' => $filters['quiz_title'] ?? null,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        return view('admin.certificates.lists', $data);
    }

    private function filters($query, $request)
    {
        $filters = $request->all();

        if (!empty($filters['student_ids'])) {
            $query->whereIn('student_id', $filters['student_ids']);
        }

        if (!empty($filters['teacher_ids'])) {
            $quizzes = Quiz::whereIn('creator_id', $filters['teacher_ids'])->pluck('id')->toArray();

            if ($quizzes and is_array($quizzes)) {
                $query->whereIn('quiz_id', $quizzes);
            }
        }

        if (!empty($filters['quiz_title'])) {
            $quizzes = Quiz::whereTranslationLike('title', '%' . $filters['quiz_title'] . '%')->pluck('id')->toArray();
            $query->whereIn('quiz_id', $quizzes);
        }

        return $query;
    }

    public function CertificatesTemplatesList(Request $request)
    {
        $this->authorize('admin_certificate_template_list');

        removeContentLocale();

        $templates = CertificateTemplate::orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.certificate_templates_list_page_title'),
            'templates' => $templates,
        ];

        return view('admin.certificates.templates', $data);
    }

    private function getElements()
    {
        $enable = [
            [
                'type' => 'switch',
                'name' => 'enable',
                'label' => trans('admin/main.active')
            ],
        ];

        $font = [
            [
                'type' => 'number_input',
                'name' => 'font_size',
                'label' => trans('admin/main.font_size')
            ],
            [
                'type' => 'color_input',
                'name' => 'font_color',
                'label' => trans('admin/main.text_color')
            ],
        ];

        $popular = [
            ...$font,
            [
                'type' => 'text_input',
                'name' => 'styles',
                'label' => trans('update.styles')
            ],
            [
                'type' => 'switch',
                'name' => 'font_weight_bold',
                'label' => trans('update.font_weight_bold')
            ],
            [
                'type' => 'switch',
                'name' => 'text_center',
                'label' => trans('update.text_center')
            ],
            [
                'type' => 'switch',
                'name' => 'text_right',
                'label' => trans('update.text_right')
            ],
            ...$enable,
        ];

        $elements = [
            'title' => [
                [
                    'type' => 'text_input',
                    'name' => 'content',
                    'label' => trans('admin/main.title')
                ],
                ...$popular,
            ],
            'subtitle' => [
                [
                    'type' => 'text_input',
                    'name' => 'content',
                    'label' => trans('admin/main.title')
                ],
                ...$popular,
            ],
            'body' => [
                [
                    'type' => 'textarea',
                    'name' => 'content',
                    'label' => trans('admin/main.description')
                ],
                ...$popular,
            ],
            'date' => [
                [
                    'type' => 'select',
                    'name' => 'display_date',
                    'label' => trans('update.display_type'),
                    'options' => [
                        'textual' => trans('update.textual'),
                        'numerical' => trans('update.numerical'),
                    ]
                ],
                ...$popular,
            ],
            'qr_code' => [
                [
                    'type' => 'select',
                    'name' => 'image_size',
                    'label' => trans('update.image_size'),
                    'options' => [
                        '128' => trans('update.128x128'),
                        '192' => trans('update.192x192'),
                        '256' => trans('update.256x256'),
                    ]
                ],
                ...$enable,
            ],
            'hint' => [
                [
                    'type' => 'text_input',
                    'name' => 'content',
                    'label' => trans('admin/main.title')
                ],
                ...$popular,
            ],
            'student_name' => [
                ...$popular,
            ],
            'instructor_name' => [
                ...$popular,
            ],
            'platform_name' => [
                ...$popular,
            ],
            'course_name' => [
                ...$popular,
            ],
            'user_certificate_additional' => [
                ...$enable,
            ],
            'instructor_signature' => [
                ...$enable,
            ],
            'platform_signature' => [
                [
                    'type' => 'file_input_manager',
                    'name' => 'image',
                    'label' => trans('admin/main.image')
                ],
                [
                    'type' => 'select',
                    'name' => 'image_size',
                    'label' => trans('update.image_size'),
                    'options' => [
                        '128' => trans('update.128x128'),
                        '192' => trans('update.192x192'),
                        '256' => trans('update.256x256'),
                    ]
                ],
                ...$enable,
            ],
            'stamp' => [
                [
                    'type' => 'file_input_manager',
                    'name' => 'image',
                    'label' => trans('admin/main.image')
                ],
                [
                    'type' => 'select',
                    'name' => 'image_size',
                    'label' => trans('update.image_size'),
                    'options' => [
                        '128' => trans('update.128x128'),
                        '192' => trans('update.192x192'),
                        '256' => trans('update.256x256'),
                    ]
                ],
                ...$enable,
            ],
        ];

        return $elements;
    }

    public function CertificatesNewTemplate()
    {
        $this->authorize('admin_certificate_template_create');

        removeContentLocale();
        $elements = $this->getElements();

        $data = [
            'pageTitle' => trans('admin/main.certificate_new_template_page_title'),
            'elements' => $elements
        ];

        return view('admin.certificates.create_template.index', $data);
    }

    public function CertificatesTemplateStore(Request $request, $template_id = null)
    {
        $this->authorize('admin_certificate_template_create');

        $this->validate($request, [
            'title' => 'required',
            'image' => 'required',
            'type' => 'required|in:quiz,course,bundle',
        ]);

        $data = $request->all();

        if (!empty($template_id)) {
            $template = CertificateTemplate::findOrFail($template_id);

            $template->update([
                'image' => $data['image'],
                'status' => $data['status'],
                'type' => $data['type'],
            ]);
        } else {
            $template = CertificateTemplate::create([
                'image' => $data['image'],
                'status' => $data['status'],
                'type' => $data['type'],
                'created_at' => time(),
            ]);
        }

        CertificateTemplateTranslation::updateOrCreate([
            'certificate_template_id' => $template->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'body' => $data['template_contents'],
            'elements' => json_encode($data['elements']),
        ]);

        removeContentLocale();

        return redirect(getAdminPanelUrl("/certificates/templates/{$template->id}/edit?locale={$data['locale']}"));
    }

    public function CertificatesTemplatePreview(Request $request)
    {
        $this->authorize('admin_certificate_template_create');

        $data = [
            'pageTitle' => trans('public.certificate'),
            'image' => $request->get('image'),
            'body' => $request->get('body'),
            'position_x' => (int)$request->get('position_x', 120),
            'position_y' => (int)$request->get('position_y', 100),
            'font_size' => (int)$request->get('font_size', 26),
            'text_color' => $request->get('text_color', '#e1e1e1'),
        ];

        $isRtl = $request->get('rtl', false);

        $body = str_replace('[student]', 'student name', $data['body']);
        $body = str_replace('[course]', 'course name', $body);
        $body = str_replace('[grade]', 'xx', $body);
        $body = str_replace('[certificate_id]', 'xx', $body);
        $body = str_replace('[user_certificate_additional]', 'xx', $body);
        $body = str_replace('[date]', 'xx', $body);
        $body = str_replace('[instructor_name]', 'xx', $body);
        $body = str_replace('[duration]', 'xx', $body);

        //$data['body'] = $body;//mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');;

        if ($isRtl) {
            $Arabic = new \I18N_Arabic('Glyphs');
            $body = $Arabic->utf8Glyphs($body);
        }

        $imgPath = public_path($data['image']);
        $img = Image::make($imgPath);

        $img->text($body, $data['position_x'], $data['position_y'], function ($font) use ($data, $isRtl) {
            $font->file($isRtl ? public_path('assets/default/fonts/vazir/Vazir-Medium.ttf') : public_path('assets/default/fonts/Montserrat-Medium.ttf'));
            $font->size($data['font_size']);
            $font->color($data['text_color']);
            $font->align($isRtl ? 'right' : 'left');
        });
        return $img->response('png');
    }

    public function CertificatesTemplatesEdit(Request $request, $template_id)
    {
        $this->authorize('admin_certificate_template_edit');

        $template = CertificateTemplate::findOrFail($template_id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $template->getTable(), $template->id);

        $elements = $this->getElements();

        $data = [
            'pageTitle' => trans('admin/main.certificate_template_edit_page_title'),
            'template' => $template,
            'elements' => $elements,
        ];

        return view('admin.certificates.create_template.index', $data);
    }

    public function CertificatesTemplatesDelete($template_id)
    {
        $this->authorize('admin_certificate_template_delete');

        $template = CertificateTemplate::findOrFail($template_id);

        $template->delete();

        return redirect(getAdminPanelUrl() . '/certificates/templates');
    }

    public function CertificatesDownload($id)
    {
        $certificate = Certificate::findOrFail($id);

        $makeCertificate = new MakeCertificate();

        if ($certificate->type == 'quiz') {
            $quizResult = QuizzesResult::where('id', $certificate->quiz_result_id)
                ->where('status', QuizzesResult::$passed)
                ->with([
                    'quiz' => function ($query) {
                        $query->with(['webinar']);
                    },
                    'user'
                ])
                ->first();

            return $makeCertificate->makeQuizCertificate($quizResult);
        } else if ($certificate->type == 'course') {

            return $makeCertificate->makeCourseCertificate($certificate);
        } else if ($certificate->type == 'bundle') {

            return $makeCertificate->makeBundleCertificate($certificate);
        }

        abort(404);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_certificate_export_excel');

        $query = Certificate::query();

        $query = $this->filters($query, $request);

        $certificates = $query
            ->whereHas('quiz')
            ->with(
                [
                    'quiz' => function ($query) {
                        $query->with('webinar');
                    },
                    'student',
                    'quizzesResult'
                ]
            )->orderBy('created_at', 'desc')
            ->get();

        $export = new CertificatesExport($certificates);

        return Excel::download($export, 'certificates.xlsx');
    }
}
