<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    |
    */

    'accepted' => ':attribute يجب قبولها.',
    'active_url' => ':attribute ليس رابطا مقبولا.',
    'after' => ':attribute يجب أن يكون تاريخا بعد :date.',
    'after_or_equal' => ':attribute يجب أن يكون تاريخا بعد أو يساوي :date.',
    'alpha' => ':attribute يحتوي ققط حروفا.',
    'alpha_dash' => ':attribute يجب أن تحتوي حروفا،أرقما شرطات.',
    'alpha_num' => ':attribute تحتوي ققط على حروف و أرقام.',
    'array' => ':attribute يجب أن تكون مصفوفة.',
    'before' => ':attribute يجب أن تكون تاريخا قبل :date.',
    'before_or_equal' => ':attribute يجب أن تكون تاريخ قبل أو يساوي :date.',
    'between' => [
        'numeric' => ':attribute يجب أن تكون بين :min و :max.',
        'file' => ':attribute يجب أن تكون بين :min و :max كيلوبايت.',
        'string' => ':attribute يجب أن تكون بين :min و :max حرفا.',
        'array' => ':attribute يجب أن تكون :min و :max عنصر.',
    ],
    'boolean' => ':attribute الحقل يجب أن يكون صح أو خطأ.',
    'confirmed' => ':attribute التأكيد غير متطابق.',
    'date' => ':attribute ليست تاريخا مقبولا.',
    'date_equals' => ':attribute يجب أن يكون تاريخا مساويا :date.',
    'date_format' => ':attribute لا يتطابق مع التنسيق :format.',
    'different' => ':attribute و :other يجب أن تكون مختلفة.',
    'digits' => ':attribute يجب أن يكون :digits رقما.',
    'digits_between' => ':attribute يجب أن تكون بين :min و :max رقما.',
    'dimensions' => ':attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => ':attribute يحتوي الحقل على قيمة مكررة.',
    'email' => ':attribute يجب أن يكون عنوان بريد إلكتروني صالحا.',
    'ends_with' => ':attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'exists' => ':attribute المختار غير مقبول.',
    'file' => ':attribute يجب أن يكون ملفا.',
    'filled' => ':attribute يجب أن يكون للحقل قيمة.',
    'gt' => [
        'numeric' => ':attribute يجب أن يكون أكبر من :value.',
        'file' => ':attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'string' => ':attribute يجب أن يكون أكبر من :value حرفا.',
        'array' => ':attribute يجب أن يكون أكثر من :value عنصر.',
    ],
    'gte' => [
        'numeric' => ':attribute يجب أن يكون أكبر من أو يساوي :value.',
        'file' => ':attribute يجب أن يكون أكبر من أو يساوي :value كيلوبايت.',
        'string' => ':attribute يجب أن يكون أكبر من أو يساوي :value حرفا.',
        'array' => ':attribute يجب أن يأخذ :value عنصر أو اكثر.',
    ],
    'image' => ':attribute يجب أن تكون صورة.',
    'in' => ':attribute  المختار مقبول .',
    'in_array' => ':attribute الحقل غير موجود في :other.',
    'integer' => ':attribute يجب أن يكون عددا صحيحا.',
    'ip' => ':attribute يجب أن تكون صالحة IP عنوان.',
    'ipv4' => ':attribute يجب أن تكون صالحة IPv4 عنوان.',
    'ipv6' => ':attribute يجب أن تكون صالحة IPv6 عنوان.',
    'json' => ':attribute يجب أن تكون صالحة JSON حرف.',
    'lt' => [
        'numeric' => ':attribute يجب أن يكون أقل من :value.',
        'file' => ':attribute يجب أن يكون أقل من :value كيلوبايت',
        'string' => ':attribute يجب أن يكون أقل من :value حرفا.',
        'array' => ':attribute يجب أن يكون أقل من :value عنصر.',
    ],
    'lte' => [
        'numeric' => ':attribute يجب أن يكون أقل من أو يساوي :value.',
        'file' => ':attribute يجب أن يكون أقل من أو يساوي :value كيلوبايت.',
        'string' => ':attribute يجب أن يكون أقل من أو يساوي :value حرف.',
        'array' => ':attribute يجب ألا يحتوي على أكثر من :value عنصر.',
    ],
    'max' => [
        'numeric' => ':attribute يجب ألا يكون أكبر من :max.',
        'file' => ':attribute يجب ألا يكون أكبر من :max كيلوبايت.',
        'string' => ':attribute يجب ألا يكون أكبر من :max حرف.',
        'array' => ':attribute قد لا يحتوي على أكثر من :max عنصر.',
    ],
    'mimes' => ':attribute يجب أن يكون ملفا من نوع: :values.',
    'mimetypes' => ':attribute يجب أن يكون ملفا من نوع: :values.',
    'min' => [
        'numeric' => ':attribute يجب أن يكون على الأقل :min.',
        'file' => ':attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'string' => ':attribute يجب أن يكون على الأقل :min حرف.',
        'array' => ':attribute يجب أن يكون على الأقل :min عنصر.',
    ],
    'not_in' => ':attribute المختار غير مقبولة.',
    'not_regex' => ':attribute تنسيق غير مقبولة.',
    'numeric' => ':attribute must be a number.',
    'password' => 'كلمة المرور غير صحيحة.',
    'password_or_username' => 'كلمة المرور أو اسم المستخدم غير صحيح.',
    'present' => ':attribute يجب أن يكون الحقل موجودا.',
    'regex' => ':attribute تنسيق غير مقبولة.',
    'required' => ':attribute الحقل مطلوب.',
    'required_if' => ':attribute الحقل مطلوب عندما :other تساوي :value.',
    'required_unless' => ':attribute الحقل مطلوب إلا إذا :other في داخل :values.',
    'required_with' => ':attribute الحقل مطلوب عندما :values موجودا.',
    'required_with_all' => ':attribute الحقل مطلوب عندما :values موجودين.',
    'required_without' => ':attribute الحقل مطلوب عندما :values غير موجود.',
    'required_without_all' => ':attribute الحقل مطلوب عندما لا شيء من :values موجودة.',
    'same' => ':attribute و :other يجب أن يتطابقا.',
    'size' => [
        'numeric' => ':attribute يجب أن يكون :size.',
        'file' => ':attribute يجب أن بكون :size كيلوبايت.',
        'string' => ':attribute يجب أن يكون :size حرف.',
        'array' => ':attribute يجب أن يحتوي :size عنصر.',
    ],
    'starts_with' => ':attribute يجب أن يبدأ بإحدى القيم: :values.',
    'string' => ':attribute يجب أن يكون حرفا.',
    'timezone' => ':attribute يجب أن يكون مجالا صالحا.',
    'unique' => ':attribute تم حجزه سابقا.',
    'uploaded' => ':attribute قشل التحميل.',
    'url' => ':attribute تنسيق غير مقبولة.',
    'uuid' => ' :attribute يجب أن تكون صالحة UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => ['rule-name' => 'رسالة-مخصصة', ],
    ],

    'captcha' => 'غير صحيحة captcha...',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
