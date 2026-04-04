@php
  // Skip breadcrumb on the front page.
  if ( is_front_page() ) {
      return;
  }

  $crumbs = array();

  // Always start with home.
  $crumbs[] = array(
      'label' => __( 'Koti', 'omakeikka-theme' ),
      'url'   => home_url( '/' ),
  );

  if ( is_singular( 'occupation' ) ) {
      $crumbs[] = array(
          'label' => __( 'Ammatit', 'omakeikka-theme' ),
          'url'   => get_post_type_archive_link( 'occupation' ),
      );
      $crumbs[] = array( 'label' => get_the_title() );

  } elseif ( is_tax( 'municipality' ) ) {
      $crumbs[] = array(
          'label' => __( 'Ammatit', 'omakeikka-theme' ),
          'url'   => get_post_type_archive_link( 'occupation' ),
      );
      $crumbs[] = array( 'label' => single_term_title( '', false ) );

  } elseif ( is_post_type_archive( 'occupation' ) ) {
      $crumbs[] = array( 'label' => __( 'Ammatit', 'omakeikka-theme' ) );

  } elseif ( is_singular() ) {
      $post_type = get_post_type();
      if ( $post_type && $post_type !== 'post' ) {
          $pt_obj = get_post_type_object( $post_type );
          if ( $pt_obj && $pt_obj->has_archive ) {
              $crumbs[] = array(
                  'label' => $pt_obj->labels->name,
                  'url'   => get_post_type_archive_link( $post_type ),
              );
          }
      }
      if ( is_singular( 'post' ) ) {
          $cats = get_the_category();
          if ( $cats ) {
              $crumbs[] = array(
                  'label' => $cats[0]->name,
                  'url'   => get_category_link( $cats[0] ),
              );
          }
      }
      $crumbs[] = array( 'label' => get_the_title() );

  } elseif ( is_page() ) {
      $ancestors = get_post_ancestors( get_the_ID() );
      foreach ( array_reverse( $ancestors ) as $ancestor_id ) {
          $crumbs[] = array(
              'label' => get_the_title( $ancestor_id ),
              'url'   => get_permalink( $ancestor_id ),
          );
      }
      $crumbs[] = array( 'label' => get_the_title() );

  } elseif ( is_archive() ) {
      $crumbs[] = array( 'label' => get_the_archive_title() );

  } elseif ( is_search() ) {
      $crumbs[] = array( 'label' => sprintf( __( 'Haun tulokset: %s', 'omakeikka-theme' ), get_search_query() ) );

  } elseif ( is_404() ) {
      $crumbs[] = array( 'label' => __( 'Sivua ei löydy', 'omakeikka-theme' ) );
  }
@endphp

@if(count($crumbs) > 1)
  <nav class="site-breadcrumb bg-light border-bottom py-2" aria-label="{{ __('Murupolku', 'omakeikka-theme') }}">
    <div class="container">
      <ol class="breadcrumb mb-0 small">
        @foreach($crumbs as $i => $crumb)
          @if($i < count($crumbs) - 1)
            <li class="breadcrumb-item">
              <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
            </li>
          @else
            <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
          @endif
        @endforeach
      </ol>
    </div>
  </nav>
@endif
