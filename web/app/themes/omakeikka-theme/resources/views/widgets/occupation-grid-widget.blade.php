@if(!empty($occupations))
  <div class="occupation-grid">
    <p class="text-muted mb-4 bg-white rounded">
      {!! wp_kses_post( sprintf(
        __( 'Alla on näyte omakeikassa olevista ammattialoista. Omakeikassa on rekisteröityneitä eläkeläisiä huomattavasti laajemmalta ammattikirjolta: <a href="/rekisteroidy/">rekisteröidy ja selaa kaikkia profiileja</a>.', 'omakeikka-theme' )
      ) ) !!}
    </p>
    <div class="row g-3">
      @foreach($occupations as $occupation)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <a href="{{ $occupation['url'] }}" class="occupation-grid__card text-decoration-none d-flex flex-column align-items-center text-center h-100">
            @if($occupation['icon_url'])
              <div class="occupation-grid__icon mb-2">
                <img
                  src="{{ $occupation['icon_url'] }}"
                  alt=""
                  width="48"
                  height="48"
                  loading="lazy"
                >
              </div>
            @endif
            <span class="occupation-grid__title">{{ $occupation['title'] }}</span>
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endif
