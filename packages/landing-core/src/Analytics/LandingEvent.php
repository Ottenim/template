<?php

namespace Template\LandingCore\Analytics;

/**
 * Vocabulário canônico de eventos de tracking compartilhado entre os módulos
 * e o analytics. Mora no core (e não no landing-analytics) para que nenhum
 * módulo de feature precise depender de outro: todos já dependem do core.
 *
 * A config de cada módulo ainda aceita um nome livre em `tracking.event_name`
 * (continua configurável); este enum é a fonte única do nome padrão e o lugar
 * para checar/descobrir os eventos conhecidos.
 */
enum LandingEvent: string
{
    case PageView = 'page_view';
    case ContactSubmit = 'contact_submit';
    case ContactFormSubmit = 'contact_form_submit';
    case LeadCaptureSubmit = 'lead_capture_submit';
    case WhatsappClick = 'whatsapp_click';
    case PricingCtaClick = 'pricing_cta_click';
    case CtaClick = 'cta_click';
    case ScrollDepth = 'scroll_depth';
}
