@if(!empty($occupation_links))
  <nav class="occupation-links" aria-label="{{ __('Ammatit', 'omakeikka-theme') }}">
    <h3 class="occupation-links__heading">{{ __('Tutustu ammatteihin', 'omakeikka-theme') }}</h3>
    <ul class="occupation-links__list list-unstyled d-flex flex-wrap gap-2">
      @foreach($occupation_links as $link)
        <li>
          <a href="{{ $link['url'] }}" class="badge bg-dark text-primary text-decoration-none">
            {{ $link['title'] }}
          </a>
        </li>
      @endforeach
    </ul>
  </nav>
@endif
