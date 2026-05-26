<?php

namespace App\Services\Audit;

class AuditDescriptionService
{
    protected array $ignored = [
        'updated_at',
        'created_at',
        'deleted_at',
        'password',
        'remember_token',
    ];

    public function make($audit): array
    {
        $model = class_basename(
            $audit->auditable_type
        );

        $event = strtolower(
            $audit->event
        );

        $oldValues = $this->parse(
            $audit->old_values
        );

        $newValues = $this->parse(
            $audit->new_values
        );

        $changes = $this->changes(
            $oldValues,
            $newValues
        );

        $narrative = $this->narrative(
            $event,
            $model,
            $changes
        );

        return [
            'title' => $this->title(
                $event,
                $model
            ),

            'event' => $event,

            'model' => $model,

            'changes' => $changes,

            'narrative' => $narrative,

            'html' => $this->html(
                $event,
                $model,
                $narrative
            ),

            'text' => $narrative,
        ];
    }

    protected function parse($values): array
    {
        if (is_array($values)) {
            return $values;
        }

        if (is_object($values)) {
            return (array) $values;
        }

        if (blank($values)) {
            return [];
        }

        if (is_string($values)) {

            $decoded = json_decode(
                $values,
                true
            );

            return is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }

    protected function changes(
        array $oldValues,
        array $newValues
    ): array {

        $changes = [];

        $keys = array_unique([
            ...array_keys($oldValues),
            ...array_keys($newValues),
        ]);

        foreach ($keys as $field) {

            if (
                in_array(
                    $field,
                    $this->ignored
                )
            ) {
                continue;
            }

            $old = $oldValues[$field] ?? null;
            $new = $newValues[$field] ?? null;

            $normalizedOld = $this->normalize($old);
            $normalizedNew = $this->normalize($new);

            if (
                $normalizedOld ===
                $normalizedNew
            ) {
                continue;
            }

            $changes[] = [
                'field' => $field,

                'label' => $this->label(
                    $field
                ),

                'old' => $normalizedOld,

                'new' => $normalizedNew,
            ];
        }

        return $changes;
    }

    protected function normalize($value): string
    {
        if (is_null($value)) {
            return 'Empty';
        }

        if (is_bool($value)) {

            return $value
                ? 'Yes'
                : 'No';
        }

        if (is_array($value)) {

            if (empty($value)) {
                return 'Empty';
            }

            return collect($value)
                ->map(
                    fn ($item) => (string) $item
                )
                ->implode(', ');
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? 'Empty'
            : $value;
    }

    protected function label(
        string $field
    ): string {

        $field = preg_replace(
            '/(?<!^)[A-Z]/',
            ' $0',
            $field
        );

        $field = str_replace(
            '_',
            ' ',
            $field
        );

        return ucwords(
            strtolower($field)
        );
    }

    protected function title(
        string $event,
        string $model
    ): string {

        return match ($event) {

            'created' =>
            "Created {$model}",

            'updated' =>
            "Updated {$model}",

            'deleted' =>
            "Deleted {$model}",

            'restored' =>
            "Restored {$model}",

            default =>
                ucfirst($event)
                . " {$model}",
        };
    }

    protected function narrative(
        string $event,
        string $model,
        array $changes
    ): string {

        if (empty($changes)) {

            return match ($event) {

                'created' =>
                "A new {$model} record was created.",

                'updated' =>
                "{$model} information was updated.",

                'deleted' =>
                "{$model} record was deleted.",

                default =>
                "{$model} activity detected.",
            };
        }

        $phrases = [];

        foreach ($changes as $change) {

            $field = strtolower(
                $change['label']
            );

            $old = $change['old'];
            $new = $change['new'];

            if ($old === 'Empty') {

                $phrases[] =
                    "{$field} was set to {$new}";
            }

            else {

                $phrases[] =
                    "{$field} changed from {$old} to {$new}";
            }
        }

        $joined = collect($phrases)
            ->implode(', ');

        return match ($event) {

            'created' =>
            "A new {$model} record was created where {$joined}.",

            'updated' =>
            "{$model} information was updated where {$joined}.",

            'deleted' =>
            "{$model} record was deleted.",

            default =>
            "{$model} activity detected where {$joined}.",
        };
    }

    protected function html(
        string $event,
        string $model,
        string $narrative
    ): string {

        return '
            <div class="audit-description ' . e($event) . '">

                <div class="audit-event-title">
                    ' . e(
                $this->title(
                    $event,
                    $model
                )
            ) . '
                </div>

                <div class="audit-narrative">
                    ' . e($narrative) . '
                </div>

            </div>
        ';
    }
}
