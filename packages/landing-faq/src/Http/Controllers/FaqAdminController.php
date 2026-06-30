<?php

namespace Template\LandingFaq\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingFaq\Config\FaqConfig;
use Template\LandingFaq\Http\Requests\StoreFaqItemRequest;
use Template\LandingFaq\Http\Requests\UpdateFaqItemRequest;
use Template\LandingFaq\Models\FaqItem;

class FaqAdminController extends Controller
{
    public function index(FaqConfig $config): View
    {
        $items = FaqItem::query()
            ->ordered()
            ->paginate($config->adminPerPage());

        return view('landing-faq::admin.index', [
            'items' => $items,
        ]);
    }

    public function create(): View
    {
        return view('landing-faq::admin.create', [
            'faqItem' => new FaqItem([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(StoreFaqItemRequest $request): RedirectResponse
    {
        FaqItem::query()->create($this->payload($request));

        return redirect()
            ->route('faq.admin.index')
            ->with('landing_faq_success', 'Pergunta criada com sucesso.');
    }

    public function edit(FaqItem $faqItem): View
    {
        return view('landing-faq::admin.edit', [
            'faqItem' => $faqItem,
        ]);
    }

    public function update(UpdateFaqItemRequest $request, FaqItem $faqItem): RedirectResponse
    {
        $faqItem->update($this->payload($request));

        return redirect()
            ->route('faq.admin.index')
            ->with('landing_faq_success', 'Pergunta atualizada com sucesso.');
    }

    public function destroy(FaqItem $faqItem): RedirectResponse
    {
        $faqItem->delete();

        return redirect()
            ->route('faq.admin.index')
            ->with('landing_faq_success', 'Pergunta removida com sucesso.');
    }

    protected function payload(FormRequest $request): array
    {
        return [
            ...$request->validated(),
            'category' => Coerce::nullableString($request->input('category')),
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
