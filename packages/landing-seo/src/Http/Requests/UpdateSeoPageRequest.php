<?php

namespace Template\LandingSeo\Http\Requests;

use Illuminate\Validation\Rule;
use Template\LandingSeo\Models\SeoPage;

class UpdateSeoPageRequest extends StoreSeoPageRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $table = config('landing-seo.database.table', 'lp_seo_pages');
        $seoPage = $this->route('seoPage');
        $seoPageId = $seoPage instanceof SeoPage ? $seoPage->getKey() : $seoPage;

        $rules['page_key'] = ['required', 'string', 'max:120', Rule::unique($table, 'page_key')->ignore($seoPageId)];

        return $rules;
    }
}
