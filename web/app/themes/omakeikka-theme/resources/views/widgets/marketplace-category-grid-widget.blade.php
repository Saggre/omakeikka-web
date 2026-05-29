@if(!empty($groups))
  <div class="marketplace-grid">
    <div class="row g-3">
      @foreach($groups as $group)
        <div class="col-6 col-md-4 col-lg-3">
          <a
            href="{{ $group['url'] }}"
            class="marketplace-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100"
          >
            @if(!empty($group['icon_class']))
              <span class="marketplace-grid__icon mb-3" aria-hidden="true">
                <i class="{{ $group['icon_class'] }}"></i>
              </span>
            @endif
            <span class="marketplace-grid__title">{{ $group['name'] }}</span>
            @if(!empty($group['categories']))
              <span class="marketplace-grid__meta mt-2">
                {{ sprintf( _n( '%d palvelu', '%d palvelua', count($group['categories']), 'omakeikka-theme' ), count($group['categories']) ) }}
              </span>
            @endif
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endif
