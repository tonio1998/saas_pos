<?php

namespace App\Helpers;

class StatusHelper
{
    public static function badge(?string $status): string
    {
        $status = strtolower(trim($status ?? ''));

        $map = [
            'active'       => ['success', 'bi-check-circle-fill', 'Active'],
            'inactive'     => ['secondary', 'bi-pause-circle-fill', 'Inactive'],
            'pending'      => ['warning', 'bi-hourglass-split', 'Pending'],
            'approved'     => ['success', 'bi-patch-check-fill', 'Approved'],
            'rejected'     => ['danger', 'bi-x-circle-fill', 'Rejected'],
            'cancelled'    => ['dark', 'bi-slash-circle-fill', 'Cancelled'],
            'completed'    => ['success', 'bi-check2-all', 'Completed'],
            'processing'   => ['info', 'bi-arrow-repeat', 'Processing'],
            'on_hold'      => ['warning', 'bi-pause-fill', 'On Hold'],
            'draft'        => ['secondary', 'bi-pencil-square', 'Draft'],
            'failed'       => ['danger', 'bi-exclamation-octagon-fill', 'Failed'],
            'success'      => ['success', 'bi-check-circle-fill', 'Success'],
            'error'        => ['danger', 'bi-bug-fill', 'Error'],
            'expired'      => ['dark', 'bi-clock-history', 'Expired'],
            'paid'         => ['success', 'bi-cash-stack', 'Paid'],
            'unpaid'       => ['danger', 'bi-cash', 'Unpaid'],
            'partial'      => ['warning', 'bi-percent', 'Partial'],
            'refunded'     => ['primary', 'bi-arrow-counterclockwise', 'Refunded'],
            'returned'     => ['info', 'bi-box-arrow-left', 'Returned'],
            'shipped'      => ['primary', 'bi-truck', 'Shipped'],
            'delivered'    => ['success', 'bi-box-seam-fill', 'Delivered'],
            'received'     => ['success', 'bi-inbox-fill', 'Received'],
            'sold'         => ['success', 'bi-cart-check-fill', 'Sold'],
            'available'    => ['success', 'bi-check-square-fill', 'Available'],
            'out_of_stock' => ['danger', 'bi-x-square-fill', 'Out of Stock'],
            'low_stock'    => ['warning', 'bi-exclamation-triangle-fill', 'Low Stock'],
            'archived'     => ['dark', 'bi-archive-fill', 'Archived'],
            'deleted'      => ['danger', 'bi-trash-fill', 'Deleted'],
            'verified'     => ['success', 'bi-patch-check-fill', 'Verified'],
            'unverified'   => ['secondary', 'bi-patch-minus-fill', 'Unverified'],
            'enabled'      => ['success', 'bi-toggle-on', 'Enabled'],
            'disabled'     => ['secondary', 'bi-toggle-off', 'Disabled'],
            'open'         => ['success', 'bi-unlock-fill', 'Open'],
            'closed'       => ['secondary', 'bi-lock-fill', 'Closed'],
            'locked'       => ['danger', 'bi-lock-fill', 'Locked'],
            'cash'         => ['success', 'bi-cash-fill', 'Cash'],
            'gcash'        => ['primary', 'bi-credit-card-2-front', 'GCash'],
            'bank_transfer'=> ['info', 'bi-wallet2', 'Bank Transfer'],
            'payment'      => ['success', 'bi-cash-stack', 'Payment'],
            'sale'         => ['success', 'bi-cart-check-fill', 'SALE'],
        ];

        [$color, $icon, $label] = $map[$status]
            ?? ['warning', 'bi-question-circle-fill', ucwords(str_replace('_', ' ', $status ?: 'Unknown'))];

        return <<<HTML
                <span class="status-badge status-{$color}">
                    <i class="bi {$icon}"></i>
                    {$label}
                </span>
                HTML;
    }
}
