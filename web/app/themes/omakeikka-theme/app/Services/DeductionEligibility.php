<?php

namespace App\Services;

enum DeductionEligibility
{
    /** Show the notice. */
    case Yes;

    /** Show the notice with a restriction, eligibility depends on the work. */
    case Caveat;

    /** Render nothing, a negative claim is also a claim. */
    case No;
}
