@if(!empty($groups))
  <div class="marketplace-grid">
    <div class="row g-3">
      @foreach($groups as $group)
        <div class="col-6 col-md-4 col-lg-3">
          <a
            href="{{ esc_url( $group['form_url'] ?? $group['url'] ) }}"
            class="marketplace-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100"
          >
            @if(!empty($group['icon_class']))
              <span class="marketplace-grid__icon mb-3" aria-hidden="true">
                <i class="{{ $group['icon_class'] }}"></i>
              </span>
            @endif
            <span class="marketplace-grid__title">{{ $group['name'] }}</span>
            @if(!empty($group['count']))
              <span class="marketplace-grid__meta mt-2">
                {{ sprintf( _n( '%d palvelu', '%d palvelua', $group['count'], 'omakeikka-theme' ), $group['count'] ) }}
              </span>
            @endif
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endif
