<?php

return [
    'drive_api' => [
        'json_config' => env('GOOGLE_DRIVE_API_JSON_CONFIG', ''),
    ],
    'analytic_report_api' => [
        'json_config' => env('GOOGLE_ANALYTIC_REPORTING_JSON_CONFIG', ''),
    ],
    'hiep_template_link' => env('GOOGLE_HIEP_TEMPLATE_LINK', ''),
    'gtf_template_link' => env('GOOGLE_GTF_TEMPLATE_LINK', ''),
    'gtf_template_link_updated' => env('GOOGLE_GTF_TEMPLATE_LINK_UPDATED', ''),
];
