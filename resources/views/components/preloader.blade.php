@php
    $letters = str_split('SISTEMA');
@endphp

<div id="sistema-preloader" class="sistema-preloader" role="status" aria-live="polite" aria-label="Memuat SISTEMA">
    <div class="sistema-preloader__word">
        @foreach ($letters as $index => $letter)
            <span class="sistema-preloader__letter" style="animation-delay: {{ $index * 110 }}ms">{{ $letter }}</span>
        @endforeach
    </div>
</div>

<noscript>
    <style>#sistema-preloader { display: none !important; }</style>
</noscript>

<script>
    (function () {
        var preloader = document.getElementById('sistema-preloader');
        if (!preloader) {
            return;
        }

        var now = function () {
            return (window.performance && performance.now) ? performance.now() : Date.now();
        };

        var startedAt = now();
        var minDuration = 420;
        var finished = false;

        var hide = function () {
            if (finished) {
                return;
            }
            finished = true;
            preloader.classList.add('is-hidden');
            preloader.setAttribute('aria-hidden', 'true');
            window.setTimeout(function () {
                if (preloader && preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
            }, 600);
        };

        var scheduleHide = function () {
            var remaining = Math.max(0, minDuration - (now() - startedAt));
            window.setTimeout(hide, remaining);
        };

        if (document.readyState === 'complete') {
            scheduleHide();
        } else {
            window.addEventListener('load', scheduleHide, { once: true });
            window.setTimeout(scheduleHide, 2200);
        }

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                hide();
            }
        });

        // Pengaman: jangan pernah membiarkan halaman terkunci.
        window.setTimeout(hide, 5000);
    })();
</script>