<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalMatrix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Initialize the approval process for a document.
     * This creates the necessary pending approval records based on the ApprovalMatrix.
     */
    public function initializeApproval(Model $approvable, float $amount, string $documentType)
    {
        $matrix = ApprovalMatrix::where('document_type', $documentType)
            ->where('min_amount', '<=', $amount)
            ->orderBy('sequence', 'asc')
            ->get();

        if ($matrix->isEmpty()) {
            // If no matrix matches, we can auto-approve or handle as needed.
            // For now, if no matrix exists, we'll assume it's auto-approved.
            $this->autoApprove($approvable);
            return;
        }

        DB::transaction(function () use ($approvable, $matrix) {
            foreach ($matrix as $step) {
                // We create a "pending" approval for each role required.
                // Note: We don't assign a specific user yet; anybody with the role can approve.
                // Or we can leave user_id null to signify "Pending any user with this role".
                // Since our Approval model has user_id, we might need to adjust it to support role_id
                // OR we just use status 'pending' and check permissions during the actual 'approve' call.
                
                Approval::create([
                    'approvable_id' => $approvable->id,
                    'approvable_type' => get_class($approvable),
                    'status' => 'pending',
                ]);
            }
        });
    }

    /**
     * Process an approval step.
     */
    public function approve(Model $approvable, int $userId, ?string $comment = null)
    {
        // Find the first pending approval for this document.
        $nextApproval = Approval::where('approvable_id', $approvable->id)
            ->where('approvable_type', get_class($approvable))
            ->where('status', 'pending')
            ->orderBy('id', 'asc') // Assuming sequential IDs follow matrix order
            ->first();

        if (!$nextApproval) {
            return; // Already fully approved or no approval needed.
        }

        $nextApproval->update([
            'user_id' => $userId,
            'status' => 'approved',
            'comment' => $comment,
        ]);

        // Check if all steps are now approved.
        $remainingSteps = Approval::where('approvable_id', $approvable->id)
            ->where('approvable_type', get_class($approvable))
            ->where('status', 'pending')
            ->exists();

        if (!$remainingSteps) {
            $this->finalizeApproval($approvable);
        }
    }

    protected function autoApprove(Model $approvable)
    {
        if (method_exists($approvable, 'markAsApproved')) {
            $approvable->markAsApproved();
        }
    }

    protected function finalizeApproval(Model $approvable)
    {
        if (method_exists($approvable, 'markAsApproved')) {
            $approvable->markAsApproved();
        }
    }
}
