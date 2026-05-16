<?php

namespace Template\LandingFaq\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingFaq\Http\Requests\StoreFaqItemRequest;
use Template\LandingFaq\Http\Requests\UpdateFaqItemRequest;
use Template\LandingFaq\Models\FaqItem;

class FaqAdminController extends Controller
{
    public function index(): View
    {
        $items = FaqItem::query()
            ->ordered()
            ->paginate((int) config('landing-faq.admin.per_page', 15));

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
            'category' => $this->nullableString($request->input('category')),
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
