@extends('layouts.app')

@section('content')

<script type="application/ld+json">{!! $json_ld !!}</script>

<div class="municipality-archive">

  <header class="municipality-archive__header py-5 bg-light">
    <div class="container">
      <nav aria-label="{{ __('Breadcrumb', 'omakeikka-wp-theme') }}">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ get_post_type_archive_link('occupation') }}">{{ __('Ammatit', 'omakeikka-wp-theme') }}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ $municipality->name }}</li>
        </ol>
      </nav>

      <h1>{{ sprintf(__('Ammatit %s', 'omakeikka-wp-theme'), $municipality->name) }}</h1>

      {{-- TODO Phase 5: Live professional count for this municipality --}}
    </div>
  </header>

  <section class="municipality-archive__occupations py-5">
    <div class="container">
      @if(!empty($occupations))
        <div class="row g-4">
          @foreach($occupations as $occupation)
            <div class="col-md-4">
              <div class="card h-100">
                <div class="card-body">
                  <h2 class="card-title h5">
                    <a href="{{ $occupation['url'] }}" class="text-decoration-none stretched-link">
                      {{ $occupation['title'] }}
                    </a>
                  </h2>
                  @if($occupation['excerpt'])
                    <p class="card-text text-muted small">{{ $occupation['excerpt'] }}</p>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p>{{ __('Ei ammatteja tässä kunnassa.', 'omakeikka-wp-theme') }}</p>
      @endif
    </div>
  </section>

</div>

@endsection
