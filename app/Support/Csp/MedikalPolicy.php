<?php

namespace App\Support\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Policy;

class MedikalPolicy extends Policy
{
    public function configure(): void
    {
        $this
            // -------------------------------------------------------
            // default-src: Diğer direktiflerin kapsamadığı her şey
            // -------------------------------------------------------
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::NONE)   // override: sadece self

            // -------------------------------------------------------
            // script-src: JavaScript kaynakları
            //   - 'self'            : kendi domainimiz
            //   - cdn.tailwindcss   : Tailwind CDN
            //   - cdn.jsdelivr.net  : Alpine.js, Chart.js, SweetAlert2, Select2
            //   - code.jquery.com   : jQuery
            //   - unpkg.com         : Alpine.js (app layout'ta farklı src kullanıyor)
            // -------------------------------------------------------
            ->addDirective(Directive::SCRIPT, Keyword::SELF)
            ->addDirective(Directive::SCRIPT, Keyword::UNSAFE_INLINE)   // inline <script> blokları için
            ->addDirective(Directive::SCRIPT, Keyword::UNSAFE_EVAL)     // Alpine.js evaluate() için
            ->addDirective(Directive::SCRIPT, 'https://cdn.tailwindcss.com')
            ->addDirective(Directive::SCRIPT, 'https://cdn.jsdelivr.net')
            ->addDirective(Directive::SCRIPT, 'https://code.jquery.com')
            ->addDirective(Directive::SCRIPT, 'https://unpkg.com')
            ->addDirective(Directive::SCRIPT, 'https://*.iyzipay.com')

            // -------------------------------------------------------
            // style-src: CSS kaynakları
            // -------------------------------------------------------
            ->addDirective(Directive::STYLE, Keyword::SELF)
            ->addDirective(Directive::STYLE, Keyword::UNSAFE_INLINE)    // inline style ve Tailwind için
            ->addDirective(Directive::STYLE, 'https://fonts.googleapis.com')
            ->addDirective(Directive::STYLE, 'https://cdn.jsdelivr.net')
            ->addDirective(Directive::STYLE, 'https://cdnjs.cloudflare.com')

            // -------------------------------------------------------
            // font-src: Web fontları
            // -------------------------------------------------------
            ->addDirective(Directive::FONT, Keyword::SELF)
            ->addDirective(Directive::FONT, 'data:')
            ->addDirective(Directive::FONT, 'https://fonts.gstatic.com')
            ->addDirective(Directive::FONT, 'https://cdnjs.cloudflare.com')
            ->addDirective(Directive::FONT, 'https://cdn.jsdelivr.net')

            // -------------------------------------------------------
            // img-src: Görseller (yerel + harici CDN + data URI)
            // -------------------------------------------------------
            ->addDirective(Directive::IMG, Keyword::SELF)
            ->addDirective(Directive::IMG, 'data:')
            ->addDirective(Directive::IMG, 'https:')
            ->addDirective(Directive::IMG, 'http:')    // yerel geliştirme (localhost/http)

            // -------------------------------------------------------
            // connect-src: AJAX / fetch / WebSocket bağlantıları
            // -------------------------------------------------------
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::CONNECT, 'https:')
            ->addDirective(Directive::CONNECT, 'http:')

            // -------------------------------------------------------
            // media-src: Ses & video
            // -------------------------------------------------------
            ->addDirective(Directive::MEDIA, Keyword::SELF)

            // -------------------------------------------------------
            // object-src: Flash vb. zararlı plugin'ler tamamen devre dışı
            // -------------------------------------------------------
            ->addDirective(Directive::OBJECT, Keyword::NONE)

            // -------------------------------------------------------
            // frame-src: iframe kaynakları
            // -------------------------------------------------------
            ->addDirective(Directive::FRAME, Keyword::SELF)
            ->addDirective(Directive::FRAME, 'https://www.youtube.com')
            ->addDirective(Directive::FRAME, 'https://player.vimeo.com')
            ->addDirective(Directive::FRAME, 'https://*.iyzipay.com')

            // -------------------------------------------------------
            // base-uri: <base> etiketini kısıtla
            // -------------------------------------------------------
            ->addDirective(Directive::BASE, Keyword::SELF)

            // -------------------------------------------------------
            // form-action: Form gönderimi sadece kendi alanımıza
            // -------------------------------------------------------
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, 'https://*.iyzipay.com')
            ->addDirective(Directive::FORM_ACTION, 'https://*.iyzico.com');
    }
}
