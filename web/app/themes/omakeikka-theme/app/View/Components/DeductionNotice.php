<?php

namespace App\View\Components;

use App\Services\DeductionEligibility;
use App\Services\HouseholdDeduction;
use Roots\Acorn\View\Component;

/**
 * Kotitalousvähennys notice for a group or a single category.
 */
class DeductionNotice extends Component
{
    public DeductionEligibility $eligibility;

    public string $updated;

    public string $infoUrl = HouseholdDeduction::INFO_URL;

    public function __construct(
        HouseholdDeduction $deduction,
        string $group = '',
        string $category = '',
        public bool $compact = false,
    ) {
        $this->eligibility = $deduction->eligibility( $group, $category );
        $this->updated     = $deduction->updatedLabel();
    }

    public function shouldRender(): bool
    {
        return DeductionEligibility::No !== $this->eligibility;
    }

    public function render()
    {
        return $this->view( 'components.deduction-notice' );
    }
}
